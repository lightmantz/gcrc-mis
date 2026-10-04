<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\Child;
use App\Models\Guardian;
use App\Models\Referral;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $roles = $user->getRoleNames();

        $stats = [
            'children' => [
                'total'  => $user->can('children.view') ? Child::count() : null,
                'active' => $user->can('children.view') ? Child::active()->count() : null,
                'new_30' => $user->can('children.view')
                    ? Child::where('registration_date', '>=', now()->subDays(30))->count()
                    : null,
            ],
            'staff' => [
                'total'  => $user->can('staff.view') ? Staff::count() : null,
                'active' => $user->can('staff.view') ? Staff::active()->count() : null,
            ],
            'guardians' => [
                'total' => $user->can('guardians.view') ? Guardian::count() : null,
            ],
            'assessments' => [
                'total'     => $user->can('assessments.view') ? Assessment::count() : null,
                'drafts'    => $user->can('assessments.view') ? Assessment::draft()->count() : null,
                'finalized' => $user->can('assessments.view') ? Assessment::finalized()->count() : null,
                'this_week' => $user->can('assessments.view')
                    ? Assessment::where('assessment_date', '>=', now()->startOfWeek())->count()
                    : null,
            ],
            'referrals' => [
                'total'    => $user->can('referrals.view') ? Referral::count() : null,
                'received' => $user->can('referrals.view')
                    ? Referral::where('status', 'received')->count()
                    : null,
            ],
            'users' => [
                'total'  => $user->can('users.view') ? User::count() : null,
                'active' => $user->can('users.view') ? User::where('is_active', true)->count() : null,
            ],
        ];

        // Recent assessments (respects permission)
        $recentAssessments = $user->can('assessments.view')
            ? Assessment::with(['child:id,child_number,first_name,last_name', 'assessor:id,first_name,last_name'])
                ->latest('assessment_date')
                ->limit(6)
                ->get()
            : collect();

        // Recent children (respects permission)
        $recentChildren = $user->can('children.view')
            ? Child::latest('registration_date')->limit(6)->get()
            : collect();

        // Children by condition — useful chart-ready data
        $childrenByCondition = $user->can('children.view')
            ? Child::selectRaw('primary_condition, count(*) as total')
                ->whereNotNull('primary_condition')
                ->groupBy('primary_condition')
                ->orderByDesc('total')
                ->pluck('total', 'primary_condition')
            : collect();

        // Pending drafts (for clinical staff to pick up)
        $pendingDrafts = $user->can('assessments.edit')
            ? Assessment::draft()
                ->with(['child:id,first_name,last_name,child_number', 'assessor:id,first_name,last_name'])
                ->latest('assessment_date')
                ->limit(5)
                ->get()
            : collect();

        // Referrals awaiting action
        $pendingReferrals = $user->can('referrals.view')
            ? Referral::whereIn('status', ['received', 'in_progress'])
                ->with('child:id,first_name,last_name,child_number')
                ->latest('referral_date')
                ->limit(5)
                ->get()
            : collect();

        return view('dashboard', compact(
            'user',
            'roles',
            'stats',
            'recentAssessments',
            'recentChildren',
            'childrenByCondition',
            'pendingDrafts',
            'pendingReferrals',
        ));
    }
}