<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Child;
use App\Models\Referral;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ReferralController extends Controller
{
    /**
     * Standalone list of all referrals across every child.
     */
    public function index(Request $request): View
    {
        $referrals = Referral::query()
            ->with([
                'child:id,child_number,first_name,middle_name,last_name',
                'registeredBy:id,name',
            ])
            ->when($request->string('status')->toString(), fn ($q, $s) => $q->where('status', $s))
            ->when($request->string('source')->toString(), fn ($q, $s) => $q->where('source_type', $s))
            ->latest('referral_date')
            ->paginate(20)
            ->withQueryString();

        return view('admin.referrals.index', compact('referrals'));
    }

    /**
     * Record a referral against a specific child.
     */
    public function store(Request $request, Child $child): RedirectResponse
    {
        $data = $this->validateReferral($request);

        $child->referrals()->create([
            ...$data,
            'registered_by' => $request->user()->id,
        ]);

        return redirect()
            ->route('admin.children.show', $child)
            ->with('status', 'Referral recorded.');
    }

    /**
     * Update an existing referral.
     */
    public function update(Request $request, Child $child, Referral $referral): RedirectResponse
    {
        abort_unless($referral->child_id === $child->id, 404);

        $referral->update($this->validateReferral($request));

        return redirect()
            ->route('admin.children.show', $child)
            ->with('status', 'Referral updated.');
    }

    /**
     * Remove a referral. Soft-deleted only — audit trail preserves history.
     */
    public function destroy(Child $child, Referral $referral): RedirectResponse
    {
        abort_unless($referral->child_id === $child->id, 404);

        $referral->delete();

        return redirect()
            ->route('admin.children.show', $child)
            ->with('status', 'Referral removed.');
    }

    /**
     * Shared validation for store/update.
     */
    private function validateReferral(Request $request): array
    {
        return $request->validate([
            'source_type' => ['required', Rule::in([
                'walk_in', 'hospital', 'clinic', 'school',
                'community', 'self', 'other',
            ])],
            'source_name' => ['nullable', 'string', 'max:200'],
            'source_contact' => ['nullable', 'string', 'max:200'],
            'referral_date' => ['required', 'date'],
            'reason' => ['nullable', 'string', 'max:2000'],
            'status' => ['required', Rule::in([
                'received', 'accepted', 'in_progress', 'completed', 'declined',
            ])],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);
    }
}