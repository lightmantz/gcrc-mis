<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreChildRequest;
use App\Http\Requests\UpdateChildRequest;
use App\Models\Child;
use App\Support\CsvExporter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ChildController extends Controller
{
    public function index(Request $request): View
    {
        $children = Child::query()
            ->when($request->string('search')->toString(), fn ($q, $s) => $q->search($s))
            ->when($request->string('condition')->toString(), fn ($q, $c) => $q->inCondition($c))
            ->when($request->string('status')->toString(), fn ($q, $s) => $q->where('status', $s))
            ->when($request->string('gender')->toString(), fn ($q, $g) => $q->where('gender', $g))
            ->latest('registration_date')
            ->paginate(20)
            ->withQueryString();

        return view('admin.children.index', compact('children'));
    }

    public function create(): View
    {
        $child = new Child();

        return view('admin.children.create', compact('child'));
    }

    public function store(StoreChildRequest $request): RedirectResponse
    {
        $child = Child::create($request->validated());

        return redirect()
            ->route('admin.children.show', $child)
            ->with('status', "Child registered as {$child->child_number}.");
    }

    public function show(Child $child): View
    {
        return view('admin.children.show', compact('child'));
    }

    public function edit(Child $child): View
    {
        return view('admin.children.edit', compact('child'));
    }

    public function update(UpdateChildRequest $request, Child $child): RedirectResponse
    {
        $child->update($request->validated());

        return redirect()
            ->route('admin.children.show', $child)
            ->with('status', "Record for {$child->full_name} updated.");
    }

    public function destroy(Child $child): RedirectResponse
    {
        $this->authorize('delete', $child);

        $name = $child->full_name;
        $child->delete();

        return redirect()
            ->route('admin.children.index')
            ->with('status', "Record for {$name} deleted.");
    }

    public function restore(int $id): RedirectResponse
    {
        $child = Child::onlyTrashed()->findOrFail($id);
        $this->authorize('restore', $child);

        $child->restore();

        return redirect()
            ->route('admin.children.show', $child)
            ->with('status', "Record for {$child->full_name} restored.");
    }

    /**
     * Print-formatted view of a single child record.
     * Optional sections enabled via query params (include_assessments, etc.).
     */
    public function printView(Child $child): View
    {
        return view('admin.children.print', compact('child'));
    }

    /**
     * Export the filtered children list to CSV.
     * Respects the same filters as the index page.
     */
    public function export(Request $request): StreamedResponse
    {
        $this->authorize('viewAny', Child::class);

        $children = Child::query()
            ->with('guardians:id,first_name,middle_name,last_name')
            ->when($request->string('search')->toString(), fn ($q, $s) => $q->search($s))
            ->when($request->string('condition')->toString(), fn ($q, $c) => $q->inCondition($c))
            ->when($request->string('status')->toString(), fn ($q, $s) => $q->where('status', $s))
            ->when($request->string('gender')->toString(), fn ($q, $g) => $q->where('gender', $g))
            ->latest('registration_date')
            ->get();

        $headers = [
            'Child Number',
            'First Name',
            'Middle Name',
            'Last Name',
            'Preferred Name',
            'Date of Birth',
            'Age',
            'Gender',
            'Phone',
            'Email',
            'District',
            'Region',
            'Primary Condition',
            'Blood Type',
            'Status',
            'Registration Date',
            'Referred By',
            'Guardians',
        ];

        $rows = $children->map(fn ($child) => [
            $child->child_number,
            $child->first_name,
            $child->middle_name,
            $child->last_name,
            $child->preferred_name,
            $child->date_of_birth?->format('Y-m-d'),
            $child->age_display,
            ucfirst($child->gender),
            $child->phone,
            $child->email,
            $child->district,
            $child->region,
            $child->primary_condition_label,
            $child->blood_type,
            $child->status_label,
            $child->registration_date?->format('Y-m-d'),
            $child->referred_by,
            $child->guardians->pluck('full_name')->implode(', '),
        ]);

        return CsvExporter::stream(
            'children-' . now()->format('Y-m-d-His') . '.csv',
            $headers,
            $rows,
        );
    }
}