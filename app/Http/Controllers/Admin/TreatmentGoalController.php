<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreGoalProgressRequest;
use App\Http\Requests\StoreTreatmentGoalRequest;
use App\Http\Requests\UpdateTreatmentGoalRequest;
use App\Models\TreatmentGoal;
use App\Models\TreatmentGoalProgress;
use App\Models\TreatmentPlan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TreatmentGoalController extends Controller
{
    /**
     * Store a new goal against a plan.
     */
    public function store(StoreTreatmentGoalRequest $request, TreatmentPlan $treatmentPlan): RedirectResponse
    {
        $data = $request->validated();

        // Place new goals at the end of the list.
        $maxOrder = $treatmentPlan->goals()->max('sort_order') ?? 0;

        $treatmentPlan->goals()->create([
            ...$data,
            'sort_order' => $maxOrder + 1,
        ]);

        return redirect()
            ->route('admin.treatment-plans.show', $treatmentPlan)
            ->with('status', 'Goal added.');
    }

    /**
     * Update a goal.
     */
    public function update(UpdateTreatmentGoalRequest $request, TreatmentPlan $treatmentPlan, TreatmentGoal $goal): RedirectResponse
    {
        abort_unless($goal->treatment_plan_id === $treatmentPlan->id, 404);

        $goal->update($request->validated());

        return redirect()
            ->route('admin.treatment-plans.show', $treatmentPlan)
            ->with('status', 'Goal updated.');
    }

    /**
     * Delete a goal. Only allowed on draft plans (policy enforces this).
     */
    public function destroy(TreatmentPlan $treatmentPlan, TreatmentGoal $goal): RedirectResponse
    {
        abort_unless($goal->treatment_plan_id === $treatmentPlan->id, 404);

        $this->authorize('delete', $goal);

        $goal->delete();

        return redirect()
            ->route('admin.treatment-plans.show', $treatmentPlan)
            ->with('status', 'Goal removed.');
    }

    /**
     * Record a progress note on a goal.
     * Optionally updates the goal's own status and progress percentage.
     */
    public function recordProgress(StoreGoalProgressRequest $request, TreatmentPlan $treatmentPlan, TreatmentGoal $goal): RedirectResponse
    {
        abort_unless($goal->treatment_plan_id === $treatmentPlan->id, 404);

        $data = $request->validated();

        // The user must have at least one linked staff record to be a
        // valid "recorded_by" — the policy already checked this on entry.
        $staff = auth()->user()->staff()->first();

        if (! $staff) {
            return back()->with('error', 'You need a linked staff record to record progress.');
        }

        TreatmentGoalProgress::create([
            'treatment_goal_id'   => $goal->id,
            'recorded_by'         => $staff->id,
            'recorded_on'         => $data['recorded_on'],
            'note'                => $data['note'],
            'progress_percentage' => $data['progress_percentage'] ?? null,
            'status_at_record'    => $data['status_at_record'] ?? $goal->status,
        ]);

        // Optionally advance the goal's own state.
        if ($data['update_goal'] ?? true) {
            $updates = [];

            if (isset($data['progress_percentage'])) {
                $updates['progress_percentage'] = $data['progress_percentage'];
            }

            if (isset($data['status_at_record'])) {
                $updates['status'] = $data['status_at_record'];
            }

            if (! empty($updates)) {
                $goal->update($updates);
            }
        }

        return redirect()
            ->route('admin.treatment-plans.show', $treatmentPlan)
            ->with('status', 'Progress note recorded.');
    }
}