<?php

namespace App\Http\Controllers\Admin;

use App\Assessments\AssessmentTypeRegistry;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAssessmentRequest;
use App\Http\Requests\UpdateAssessmentRequest;
use App\Models\Assessment;
use App\Models\Child;
use App\Models\Staff;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AssessmentController extends Controller
{
    public function index(Request $request): View
    {
        $assessments = Assessment::query()
            ->with([
                'child:id,child_number,first_name,middle_name,last_name',
                'assessor:id,first_name,middle_name,last_name,category',
            ])
            ->when($request->string('type')->toString(), fn ($q, $t) => $q->where('type', $t))
            ->when($request->string('status')->toString(), fn ($q, $s) => $q->where('status', $s))
            ->when($request->string('child')->toString(), fn ($q, $c) => $q->where('child_id', $c))
            ->latest('assessment_date')
            ->paginate(20)
            ->withQueryString();

        return view('admin.assessments.index', compact('assessments'));
    }

    public function create(Request $request): View
    {
        // Optional pre-fill from the child profile page: /assessments/create?child_id=12
        $child = null;
        if ($id = $request->integer('child_id')) {
            $child = Child::find($id);
        }

        $children = Child::active()
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get(['id', 'child_number', 'first_name', 'middle_name', 'last_name']);

        $staff = Staff::active()
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get(['id', 'first_name', 'middle_name', 'last_name', 'category']);

        return view('admin.assessments.create', compact('child', 'children', 'staff'));
    }

    public function store(StoreAssessmentRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $assessment = Assessment::create([
            'child_id'          => $data['child_id'],
            'assessor_id'       => $data['assessor_id'],
            'type'              => $data['type'],
            'assessment_date'   => $data['assessment_date'],
            'status'            => 'draft',
            'summary'           => $data['summary'] ?? null,
            'recommendations'   => $data['recommendations'] ?? null,
            'findings'          => $data['findings'],
        ]);

        return redirect()
            ->route('admin.assessments.show', $assessment)
            ->with('status', 'Draft assessment created.');
    }

    public function show(Assessment $assessment): View
    {
        $assessment->load([
            'child',
            'assessor',
            'finalizedBy',
            'documents.uploadedBy',
        ]);

        $type = $assessment->typeInstance();

        return view('admin.assessments.show', compact('assessment', 'type'));
    }

    public function edit(Assessment $assessment): View
    {
        $this->authorize('update', $assessment);

        $assessment->load(['child', 'assessor']);

        $type = $assessment->typeInstance();

        if (! $type) {
            abort(500, 'This assessment type is no longer registered.');
        }

        $staff = Staff::active()
            ->where('category', $type->assessorCategory())
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get(['id', 'first_name', 'middle_name', 'last_name', 'category']);

        return view('admin.assessments.edit', compact('assessment', 'type', 'staff'));
    }

    public function update(UpdateAssessmentRequest $request, Assessment $assessment): RedirectResponse
    {
        $data = $request->validated();

        $assessment->update([
            'assessor_id'       => $data['assessor_id'],
            'assessment_date'   => $data['assessment_date'],
            'summary'           => $data['summary'] ?? null,
            'recommendations'   => $data['recommendations'] ?? null,
            'findings'          => $data['findings'],
        ]);

        return redirect()
            ->route('admin.assessments.show', $assessment)
            ->with('status', 'Assessment updated.');
    }

    public function finalize(Assessment $assessment): RedirectResponse
    {
        $this->authorize('finalize', $assessment);

        $assessment->update([
            'status'       => 'finalized',
            'finalized_at' => now(),
            'finalized_by' => auth()->id(),
        ]);

        return redirect()
            ->route('admin.assessments.show', $assessment)
            ->with('status', 'Assessment finalized. Further edits are no longer possible.');
    }

    public function destroy(Assessment $assessment): RedirectResponse
    {
        $this->authorize('delete', $assessment);

        $childId = $assessment->child_id;
        $assessment->delete();

        return redirect()
            ->route('admin.children.show', $childId)
            ->with('status', 'Draft assessment deleted.');
    }

    public function restore(int $id): RedirectResponse
    {
        $assessment = Assessment::onlyTrashed()->findOrFail($id);
        $this->authorize('restore', $assessment);

        $assessment->restore();

        return redirect()
            ->route('admin.assessments.show', $assessment)
            ->with('status', 'Assessment restored.');
    }
}