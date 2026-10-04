<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreGuardianRequest;
use App\Http\Requests\UpdateGuardianRequest;
use App\Models\Guardian;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GuardianController extends Controller
{
    public function index(Request $request): View
    {
        $guardians = Guardian::query()
            ->withCount('children')
            ->when($request->string('search')->toString(), fn ($q, $s) => $q->search($s))
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->paginate(20)
            ->withQueryString();

        return view('admin.guardians.index', compact('guardians'));
    }

    public function create(): View
    {
        $guardian = new Guardian();

        return view('admin.guardians.create', compact('guardian'));
    }

    public function store(StoreGuardianRequest $request): RedirectResponse
    {
        $guardian = Guardian::create($request->validated());

        return redirect()
            ->route('admin.guardians.show', $guardian)
            ->with('status', "Guardian {$guardian->full_name} registered.");
    }

    public function show(Guardian $guardian): View
    {
        $guardian->load(['children' => function ($q) {
            $q->select('children.id', 'children.child_number', 'children.first_name', 'children.middle_name', 'children.last_name', 'children.date_of_birth');
        }]);

        return view('admin.guardians.show', compact('guardian'));
    }

    public function edit(Guardian $guardian): View
    {
        return view('admin.guardians.edit', compact('guardian'));
    }

    public function update(UpdateGuardianRequest $request, Guardian $guardian): RedirectResponse
    {
        $guardian->update($request->validated());

        return redirect()
            ->route('admin.guardians.show', $guardian)
            ->with('status', "Guardian {$guardian->full_name} updated.");
    }

    public function destroy(Guardian $guardian): RedirectResponse
    {
        $this->authorize('delete', $guardian);

        $name = $guardian->full_name;
        $guardian->delete();

        return redirect()
            ->route('admin.guardians.index')
            ->with('status', "Guardian {$name} deleted.");
    }

    public function restore(int $id): RedirectResponse
    {
        $guardian = Guardian::onlyTrashed()->findOrFail($id);
        $this->authorize('restore', $guardian);

        $guardian->restore();

        return redirect()
            ->route('admin.guardians.show', $guardian)
            ->with('status', "Guardian {$guardian->full_name} restored.");
    }
}