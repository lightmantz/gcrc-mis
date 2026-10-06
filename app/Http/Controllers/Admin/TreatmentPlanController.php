<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTreatmentPlanRequest;
use App\Http\Requests\UpdateTreatmentPlanRequest;
use App\Models\Child;
use App\Models\Diagnosis;
use App\Models\Staff;
use App\Models\TreatmentPlan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TreatmentPlanController extends Controller
{
    public function index(Request $request): View
    {
        $plans = TreatmentPlan::query()
            ->with([
                'child:id,child_number,first_name,middle_name,last_name',
                'leadStaff:id,first_name,middle_name,last_name,category',
            ])
            ->withCount('goals')
            ->when($request->string('child')->toString(), fn ($q, $c) => $q->where('child_id', $c))
            ->when($request->string('discipline')->toString(), fn ($q, $d) => $q->where('discipline', $d))
            ->when($request->string('status')->toString(), fn ($q, $s) => $q->where('status', $s))
            ->when($request->string('lead')->toString(), fn ($q, $l) => $q->where('lead_staff_id', $l))
            ->when($request->boolean('overdue'), fn ($q) => $q->overdueReview())
            ->latest('start_date')
            ->paginate(20)
            ->withQueryString();

        return view('admin.treatment-plans.index', compact('plans'));
    }

    public function create(Request $request): View
    {
        $child = null;
        if ($id = $request->integer('child_id')) {
            $child = Child::find($id);
        }

        $children = Child::active()
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get(['id', 'child_number', 'first_name', 'middle_name', 'last_name']);

        $staff = Staff::active()
            ->whereIn('category', ['clinical', 'therapy', 'education'])
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get(['id', 'first_name', 'middle_name', 'last_name', 'category']);

        // Preload diagnoses if a child was selected
        $diagnoses = $child
            ? $child->activeDiagnoses()->get()
            : collect();

        return view('admin.treatment-plans.create', compact(
            'child', 'children', 'staff', 'diagnoses'
        ));
    }

    public function store(StoreTreatmentPlanRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $plan = TreatmentPlan::create([
            'child_id'             => $data['child_id'],
            'lead_staff_id'        => $data['lead_staff_id'],
            'discipline'           => $data['discipline'],
            'status'               => 'draft',
            'start_date'           => $data['start_date'],
            'target_review_date'   => $data['target_review_date'] ?? null,
            'end_date'             => $data['end_date'] ?? null,
            'review_cycle'         => $data['review_cycle'],
            'next_review_date'     => $data['next_review_date'] ?? null,
            'overall_objectives'   => $data['overall_objectives'] ?? null,
            'notes'                => $data['notes'] ?? null,
        ]);

        // Attach diagnoses and team members
        $plan->diagnoses()->sync($data['diagnosis_ids'] ?? []);

        $team = collect($data['team_members'] ?? [])
            ->keyBy('staff_id')
            ->map(fn ($m) => ['role' => $m['role'] ?? null])
            ->all();

        $plan->teamMembers()->sync($team);

        return redirect()
            ->route('admin.treatment-plans.show', $plan)
            ->with('status', "Treatment plan created as draft. Add goals and activate when ready.");
    }

    public function show(TreatmentPlan $treatmentPlan): View
    {
        $treatmentPlan->load([
            'child',
            'leadStaff',
            'teamMembers',
            'diagnoses.diagnosedBy',
            'goals.progressNotes.recordedBy',
            'activatedBy',
            'closedBy',
        ]);

        return view('admin.treatment-plans.show', ['plan' => $treatmentPlan]);
    }

    public function edit(TreatmentPlan $treatmentPlan): View
    {
        $this->authorize('update', $treatmentPlan);

        $treatmentPlan->load(['child', 'diagnoses', 'teamMembers']);

        $staff = Staff::active()
            ->whereIn('category', ['clinical', 'therapy', 'education'])
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get(['id', 'first_name', 'middle_name', 'last_name', 'category']);

        $diagnoses = $treatmentPlan->child->activeDiagnoses()->get();

        return view('admin.treatment-plans.edit', [
            'plan' => $treatmentPlan,
            'staff' => $staff,
            'diagnoses' => $diagnoses,
        ]);
    }

    public function update(UpdateTreatmentPlanRequest $request, TreatmentPlan $treatmentPlan): RedirectResponse
    {
        $data = $request->validated();

        $treatmentPlan->update([
            'lead_staff_id'      => $data['lead_staff_id'],
            'start_date'         => $data['start_date'],
            'target_review_date' => $data['target_review_date'] ?? null,
            'end_date'           => $data['end_date'] ?? null,
            'review_cycle'       => $data['review_cycle'],
            'next_review_date'   => $data['next_review_date'] ?? null,
            'overall_objectives' => $data['overall_objectives'] ?? null,
            'notes'              => $data['notes'] ?? null,
        ]);

        $treatmentPlan->diagnoses()->sync($data['diagnosis_ids'] ?? []);

        $team = collect($data['team_members'] ?? [])
            ->keyBy('staff_id')
            ->map(fn ($m) => ['role' => $m['role'] ?? null])
            ->all();

        $treatmentPlan->teamMembers()->sync($team);

        return redirect()
            ->route('admin.treatment-plans.show', $treatmentPlan)
            ->with('status', 'Treatment plan updated.');
    }

    public function activate(TreatmentPlan $treatmentPlan): RedirectResponse
    {
        $this->authorize('activate', $treatmentPlan);

        // A plan needs at least one goal to be activated.
        if ($treatmentPlan->goals()->count() === 0) {
            return back()->with('error', 'Add at least one goal before activating the plan.');
        }

        $treatmentPlan->update([
            'status'       => 'active',
            'activated_at' => now(),
            'activated_by' => auth()->id(),
        ]);

        return redirect()
            ->route('admin.treatment-plans.show', $treatmentPlan)
            ->with('status', 'Treatment plan activated.');
    }

    public function close(Request $request, TreatmentPlan $treatmentPlan): RedirectResponse
    {
        $this->authorize('close', $treatmentPlan);

        $data = $request->validate([
            'status' => ['required', 'in:completed,cancelled'],
            'end_date' => ['required', 'date', 'after_or_equal:' . $treatmentPlan->start_date->toDateString()],
            'closure_reason' => ['required', 'string', 'max:2000'],
        ]);

        $treatmentPlan->update([
            'status'         => $data['status'],
            'end_date'       => $data['end_date'],
            'closed_at'      => now(),
            'closed_by'      => auth()->id(),
            'closure_reason' => $data['closure_reason'],
        ]);

        return redirect()
            ->route('admin.treatment-plans.show', $treatmentPlan)
            ->with('status', "Treatment plan marked as {$treatmentPlan->status_label}.");
    }

    public function destroy(TreatmentPlan $treatmentPlan): RedirectResponse
    {
        $this->authorize('delete', $treatmentPlan);

        $childId = $treatmentPlan->child_id;
        $label = $treatmentPlan->discipline_label;

        $treatmentPlan->delete();

        return redirect()
            ->route('admin.children.show', $childId)
            ->with('status', "Draft {$label} plan deleted.");
    }

    public function restore(int $id): RedirectResponse
    {
        $plan = TreatmentPlan::onlyTrashed()->findOrFail($id);
        $this->authorize('restore', $plan);

        $plan->restore();

        return redirect()
            ->route('admin.treatment-plans.show', $plan)
            ->with('status', 'Treatment plan restored.');
    }
}