<?php

namespace App\Http\Controllers;

use App\Diagnoses\DiagnosisVocabulary;
use App\Models\Assessment;
use App\Models\Child;
use App\Models\Diagnosis;
use App\Models\Guardian;
use App\Models\Referral;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $roles = $user->getRoleNames();

        // ─── Headline stats ────────────────────────────
        $stats = [
            'children' => $user->can('children.view') ? [
                'total' => Child::count(),
                'active' => Child::active()->count(),
                'new_30' => Child::where('registration_date', '>=', now()->subDays(30))->count(),
                'new_pct' => $this->percentChange(
                    Child::where('registration_date', '>=', now()->subDays(30))->count(),
                    Child::whereBetween('registration_date', [now()->subDays(60), now()->subDays(30)])->count(),
                ),
            ] : null,

            'assessments' => $user->can('assessments.view') ? [
                'total' => Assessment::count(),
                'this_month' => Assessment::where('assessment_date', '>=', now()->startOfMonth())->count(),
                'drafts' => Assessment::draft()->count(),
                'finalized' => Assessment::finalized()->count(),
                'finalize_rate' => $this->rate(
                    Assessment::finalized()->count(),
                    Assessment::count(),
                ),
            ] : null,

            'diagnoses' => $user->can('diagnoses.view') ? [
                'total' => Diagnosis::count(),
                'active' => Diagnosis::active()->count(),
                'this_month' => Diagnosis::where('diagnosed_at', '>=', now()->startOfMonth())->count(),
                'provisional' => Diagnosis::provisional()->count(),
            ] : null,

            'referrals' => $user->can('referrals.view') ? [
                'total' => Referral::count(),
                'new_30' => Referral::where('referral_date', '>=', now()->subDays(30))->count(),
                'pending' => Referral::whereIn('status', ['received', 'in_progress'])->count(),
                'completed' => Referral::where('status', 'completed')->count(),
            ] : null,

            'staff' => $user->can('staff.view') ? [
                'total' => Staff::count(),
                'active' => Staff::active()->count(),
                'clinical' => Staff::active()->whereIn('category', ['clinical', 'therapy'])->count(),
            ] : null,

            'guardians' => $user->can('guardians.view') ? [
                'total' => Guardian::count(),
            ] : null,

            'users' => $user->can('users.view') ? [
                'total' => User::count(),
                'active' => User::where('is_active', true)->count(),
            ] : null,
        ];

        // ─── Summary collections (used elsewhere) ──────
        $registrationsByMonth = $user->can('children.view')
            ? $this->monthlyCounts(Child::query(), 'registration_date', 6)
            : collect();

        $childrenByCondition = $user->can('children.view')
            ? Child::selectRaw('primary_condition, count(*) as total')
                ->whereNotNull('primary_condition')
                ->groupBy('primary_condition')
                ->orderByDesc('total')
                ->pluck('total', 'primary_condition')
            : collect();

        $referralSources = $user->can('referrals.view')
            ? Referral::selectRaw('source_type, count(*) as total')
                ->groupBy('source_type')
                ->orderByDesc('total')
                ->pluck('total', 'source_type')
            : collect();

        $diagnosesByCategory = $user->can('diagnoses.view')
            ? Diagnosis::active()
                ->get()
                ->groupBy(fn ($d) => $d->category_label ?? 'Uncategorised')
                ->map->count()
                ->sortDesc()
            : collect();

        $staffByCategory = $user->can('staff.view')
            ? Staff::selectRaw('category, count(*) as total')
                ->where('status', 'active')
                ->groupBy('category')
                ->orderByDesc('total')
                ->pluck('total', 'category')
            : collect();

        // ═══════════════════════════════════════════════════════════
        //  Chart datasets (consumed by ApexCharts in dashboard.blade)
        // ═══════════════════════════════════════════════════════════

        // ─── Chart 1: Children registered per month (12 months) ─────
        $registrationChart = ['labels' => [], 'values' => []];

        if ($user->can('children.view')) {
            $start = now()->subMonths(11)->startOfMonth();

            $counts = Child::query()
                ->where('registration_date', '>=', $start)
                ->selectRaw("DATE_FORMAT(registration_date, '%Y-%m') as ym, count(*) as total")
                ->groupBy('ym')
                ->pluck('total', 'ym');

            for ($i = 11; $i >= 0; $i--) {
                $date = now()->subMonths($i);
                $key = $date->format('Y-m');

                $registrationChart['labels'][] = $date->format('M Y');
                $registrationChart['values'][] = (int) ($counts[$key] ?? 0);
            }
        }

        // ─── Chart 2: Children by condition (donut) ─────────────────
        $conditionChart = ['labels' => [], 'values' => []];

        if ($user->can('children.view')) {
            $byCondition = Child::query()
                ->whereNotNull('primary_condition')
                ->selectRaw('primary_condition, count(*) as total')
                ->groupBy('primary_condition')
                ->orderByDesc('total')
                ->get();

            foreach ($byCondition as $row) {
                $sample = Child::where('primary_condition', $row->primary_condition)->first();
                $conditionChart['labels'][] = $sample?->primary_condition_label
                    ?? ucfirst(str_replace('_', ' ', $row->primary_condition));
                $conditionChart['values'][] = (int) $row->total;
            }
        }

        // ─── Chart 3: Assessment activity per month (stacked bar) ───
        $assessmentChart = ['labels' => [], 'draft' => [], 'finalized' => []];

        if ($user->can('assessments.view')) {
            $start = now()->subMonths(11)->startOfMonth();

            $rows = Assessment::query()
                ->where('assessment_date', '>=', $start)
                ->selectRaw("DATE_FORMAT(assessment_date, '%Y-%m') as ym, status, count(*) as total")
                ->groupBy('ym', 'status')
                ->get()
                ->groupBy('ym');

            for ($i = 11; $i >= 0; $i--) {
                $date = now()->subMonths($i);
                $key = $date->format('Y-m');

                $monthRows = $rows[$key] ?? collect();

                $assessmentChart['labels'][] = $date->format('M Y');
                $assessmentChart['draft'][] = (int) ($monthRows->firstWhere('status', 'draft')->total ?? 0);
                $assessmentChart['finalized'][] = (int) ($monthRows->firstWhere('status', 'finalized')->total ?? 0);
            }
        }

        // ─── Chart 4: Referral sources (bar) ────────────────────────
        $referralChart = ['labels' => [], 'values' => []];

        if ($user->can('referrals.view')) {
            $bySource = Referral::query()
                ->selectRaw('source_type, count(*) as total')
                ->groupBy('source_type')
                ->orderByDesc('total')
                ->get();

            foreach ($bySource as $row) {
                $referralChart['labels'][] = ucfirst(str_replace('_', ' ', $row->source_type));
                $referralChart['values'][] = (int) $row->total;
            }
        }

        // ─── Chart 5: Diagnoses by severity (pie) ───────────────────
        $severityChart = ['labels' => [], 'values' => []];

        if ($user->can('diagnoses.view')) {
            $bySeverity = Diagnosis::query()
                ->where('status', 'active')
                ->selectRaw('severity, count(*) as total')
                ->groupBy('severity')
                ->orderByDesc('total')
                ->get();

            foreach ($bySeverity as $row) {
                $severityChart['labels'][] = ucfirst($row->severity);
                $severityChart['values'][] = (int) $row->total;
            }
        }

        // ─── Chart 6: Staff by category (donut) ─────────────────────
        $staffChart = ['labels' => [], 'values' => []];

        if ($user->can('staff.view')) {
            $rows = Staff::query()
                ->where('status', 'active')
                ->selectRaw('category, count(*) as total')
                ->groupBy('category')
                ->orderByDesc('total')
                ->get();

            foreach ($rows as $row) {
                $staffChart['labels'][] = ucfirst(str_replace('_', ' ', $row->category));
                $staffChart['values'][] = (int) $row->total;
            }
        }

        // ─── Recent activity feed ──────────────────────
        $activity = $this->recentActivity($user);

        // ─── Recent assessments table ──────────────────
        $recentAssessments = $user->can('assessments.view')
            ? Assessment::with([
                'child:id,child_number,first_name,last_name',
                'assessor:id,first_name,last_name',
            ])->latest('assessment_date')->limit(5)->get()
            : collect();

        // ─── Pending items ─────────────────────────────
        $pendingItems = collect();

        if ($user->can('assessments.edit')) {
            foreach (Assessment::draft()->with('child:id,first_name,last_name')->latest()->limit(3)->get() as $draft) {
                $pendingItems->push([
                    'label' => "Finalize {$draft->type_label}",
                    'subtitle' => $draft->child->full_name ?? '—',
                    'url' => route('admin.assessments.show', $draft),
                    'date' => $draft->assessment_date,
                ]);
            }
        }

        if ($user->can('referrals.view')) {
            foreach (Referral::where('status', 'received')->with('child:id,first_name,last_name')->latest()->limit(3)->get() as $ref) {
                $pendingItems->push([
                    'label' => "Review referral — {$ref->source_type_label}",
                    'subtitle' => $ref->child->full_name ?? '—',
                    'url' => $ref->child ? route('admin.children.show', $ref->child) : route('admin.referrals.index'),
                    'date' => $ref->referral_date,
                ]);
            }
        }

        $pendingItems = $pendingItems->sortBy('date')->take(5);

        return view('dashboard', compact(
            'user',
            'roles',
            'stats',
            'registrationsByMonth',
            'childrenByCondition',
            'referralSources',
            'diagnosesByCategory',
            'staffByCategory',
            'activity',
            'recentAssessments',
            'pendingItems',
            'registrationChart',
            'conditionChart',
            'assessmentChart',
            'referralChart',
            'severityChart',
            'staffChart',
        ));
    }

    /**
     * Recent activity: children registered, assessments finalized,
     * diagnoses recorded — merged into one chronological feed.
     */
    private function recentActivity(User $user)
    {
        $items = collect();

        if ($user->can('children.view')) {
            foreach (Child::latest('created_at')->limit(5)->get() as $child) {
                $items->push([
                    'icon' => 'users',
                    'tone' => 'teal',
                    'text' => "New child registered — {$child->full_name}",
                    'url' => route('admin.children.show', $child),
                    'time' => $child->created_at,
                ]);
            }
        }

        if ($user->can('assessments.view')) {
            foreach (Assessment::with('child:id,first_name,last_name')->latest('created_at')->limit(5)->get() as $a) {
                $items->push([
                    'icon' => 'clipboard',
                    'tone' => $a->isFinalized() ? 'green' : 'yellow',
                    'text' => "{$a->type_label} " . ($a->isFinalized() ? 'finalized' : 'drafted') . ' — ' . ($a->child->full_name ?? '—'),
                    'url' => route('admin.assessments.show', $a),
                    'time' => $a->created_at,
                ]);
            }
        }

        if ($user->can('diagnoses.view')) {
            foreach (Diagnosis::with('child:id,first_name,last_name')->latest('created_at')->limit(5)->get() as $d) {
                $items->push([
                    'icon' => 'medical',
                    'tone' => 'blue',
                    'text' => "Diagnosis recorded — {$d->display_label} ({$d->child->full_name})",
                    'url' => route('admin.diagnoses.show', $d),
                    'time' => $d->created_at,
                ]);
            }
        }

        return $items->sortByDesc('time')->take(6);
    }

    /**
     * Count per month for the last $months months.
     */
    private function monthlyCounts($query, string $column, int $months)
    {
        $start = now()->subMonths($months - 1)->startOfMonth();

        $counts = $query
            ->where($column, '>=', $start)
            ->selectRaw("DATE_FORMAT({$column}, '%Y-%m') as ym, count(*) as total")
            ->groupBy('ym')
            ->pluck('total', 'ym');

        $filled = collect();
        for ($i = $months - 1; $i >= 0; $i--) {
            $key = now()->subMonths($i)->format('Y-m');
            $filled->put(now()->subMonths($i)->format('M'), $counts[$key] ?? 0);
        }

        return $filled;
    }

    private function percentChange(int $current, int $previous): ?int
    {
        if ($previous === 0) {
            return $current > 0 ? 100 : 0;
        }

        return (int) round((($current - $previous) / $previous) * 100);
    }

    private function rate(int $part, int $total): ?float
    {
        if ($total === 0) {
            return null;
        }

        return round(($part / $total) * 100, 1);
    }
}