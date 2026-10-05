<?php

namespace App\Http\Controllers\Admin;

use App\Diagnoses\DiagnosisVocabulary;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDiagnosisRequest;
use App\Http\Requests\UpdateDiagnosisRequest;
use App\Models\Assessment;
use App\Models\Child;
use App\Models\Diagnosis;
use App\Models\Staff;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DiagnosisController extends Controller
{
    public function index(Request $request): View
    {
        $diagnoses = Diagnosis::query()
            ->with([
                'child:id,child_number,first_name,middle_name,last_name',
                'diagnosedBy:id,first_name,middle_name,last_name',
            ])
            ->when($request->string('child')->toString(), fn ($q, $c) => $q->where('child_id', $c))
            ->when($request->string('condition')->toString(), fn ($q, $k) => $q->where('condition_key', $k))
            ->when($request->string('status')->toString(), fn ($q, $s) => $q->where('status', $s))
            ->when($request->string('type')->toString(), fn ($q, $t) => $q->where('diagnosis_type', $t))
            ->latest('diagnosed_at')
            ->paginate(20)
            ->withQueryString();

        return view('admin.diagnoses.index', compact('diagnoses'));
    }

    public function create(Request $request): View
    {
        $child = null;
        if ($id = $request->integer('child_id')) {
            $child = Child::find($id);
        }

        $assessment = null;
        if ($assessmentId = $request->integer('assessment_id')) {
            $assessment = Assessment::with('child')->find($assessmentId);
            if ($assessment && ! $child) {
                $child = $assessment->child;
            }
        }

        $children = Child::active()
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get(['id', 'child_number', 'first_name', 'middle_name', 'last_name']);

        $staff = Staff::active()
            ->whereIn('category', ['clinical', 'therapy'])
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get(['id', 'first_name', 'middle_name', 'last_name', 'category']);

        $vocabulary = DiagnosisVocabulary::grouped();

        return view('admin.diagnoses.create', compact(
            'child', 'children', 'staff', 'vocabulary', 'assessment'
        ));
    }

    public function store(StoreDiagnosisRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $data['condition_label'] = $this->resolveConditionLabel(
            $data['condition_key'],
            $data['condition_other'] ?? null
        );

        $diagnosis = Diagnosis::create($data);

        return redirect()
            ->route('admin.diagnoses.show', $diagnosis)
            ->with('status', "Diagnosis \"{$diagnosis->display_label}\" recorded.");
    }

    public function show(Diagnosis $diagnosis): View
    {
        $diagnosis->load(['child', 'diagnosedBy', 'assessment']);

        $siblingDiagnoses = $diagnosis->child->diagnoses()
            ->where('id', '!=', $diagnosis->id)
            ->orderByDesc('diagnosed_at')
            ->get();

        return view('admin.diagnoses.show', compact('diagnosis', 'siblingDiagnoses'));
    }

    public function edit(Diagnosis $diagnosis): View
    {
        $this->authorize('update', $diagnosis);

        $diagnosis->load(['child', 'diagnosedBy']);

        $staff = Staff::active()
            ->whereIn('category', ['clinical', 'therapy'])
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get(['id', 'first_name', 'middle_name', 'last_name', 'category']);

        $vocabulary = DiagnosisVocabulary::grouped();

        return view('admin.diagnoses.edit', compact('diagnosis', 'staff', 'vocabulary'));
    }

    public function update(UpdateDiagnosisRequest $request, Diagnosis $diagnosis): RedirectResponse
    {
        $data = $request->validated();

        $data['condition_label'] = $this->resolveConditionLabel(
            $data['condition_key'],
            $data['condition_other'] ?? null
        );

        $diagnosis->update($data);

        return redirect()
            ->route('admin.diagnoses.show', $diagnosis)
            ->with('status', 'Diagnosis updated.');
    }

    public function close(Request $request, Diagnosis $diagnosis): RedirectResponse
    {
        $this->authorize('close', $diagnosis);

        $data = $request->validate([
            'status' => ['required', 'in:resolved,ruled_out,transferred'],
            'ended_at' => ['required', 'date', 'after_or_equal:' . $diagnosis->diagnosed_at->toDateString()],
            'ended_reason' => ['required', 'string', 'max:1000'],
        ]);

        $diagnosis->update([
            'status' => $data['status'],
            'ended_at' => $data['ended_at'],
            'ended_reason' => $data['ended_reason'],
        ]);

        return redirect()
            ->route('admin.diagnoses.show', $diagnosis)
            ->with('status', "Diagnosis marked as {$diagnosis->status_label}.");
    }

    public function destroy(Diagnosis $diagnosis): RedirectResponse
    {
        $this->authorize('delete', $diagnosis);

        $childId = $diagnosis->child_id;
        $label = $diagnosis->display_label;

        $diagnosis->delete();

        return redirect()
            ->route('admin.children.show', $childId)
            ->with('status', "Diagnosis \"{$label}\" deleted.");
    }

    public function restore(int $id): RedirectResponse
    {
        $diagnosis = Diagnosis::onlyTrashed()->findOrFail($id);
        $this->authorize('restore', $diagnosis);

        $diagnosis->restore();

        return redirect()
            ->route('admin.diagnoses.show', $diagnosis)
            ->with('status', 'Diagnosis restored.');
    }

    /**
     * Compute the human-readable condition label from the vocabulary
     * or the "other" free-text field.
     */
    private function resolveConditionLabel(string $key, ?string $other): string
    {
        if ($key === 'other') {
            return $other ?: 'Other';
        }

        return DiagnosisVocabulary::label($key);
    }
}