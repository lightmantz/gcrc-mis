<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStaffRequest;
use App\Http\Requests\UpdateStaffRequest;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StaffController extends Controller
{
    public function index(Request $request): View
    {
        $staff = Staff::query()
            ->with('users:id,name')
            ->when($request->string('search')->toString(), function ($q, $s) {
                $q->where(function ($q) use ($s) {
                    $q->where('first_name', 'like', "%{$s}%")
                      ->orWhere('last_name', 'like', "%{$s}%")
                      ->orWhere('staff_number', 'like', "%{$s}%")
                      ->orWhere('phone', 'like', "%{$s}%");
                });
            })
            ->when($request->string('category')->toString(), fn ($q, $c) => $q->where('category', $c))
            ->when($request->string('department')->toString(), fn ($q, $d) => $q->where('department', $d))
            ->when($request->string('status')->toString(), fn ($q, $s) => $q->where('status', $s))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $departments = Staff::query()
            ->whereNotNull('department')
            ->distinct()
            ->orderBy('department')
            ->pluck('department');

        return view('admin.staff.index', compact('staff', 'departments'));
    }

    public function create(): View
    {
        $users = User::active()
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        return view('admin.staff.create', compact('users'));
    }

    public function store(StoreStaffRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $staff = Staff::create($data);

        $staff->users()->sync($data['user_ids'] ?? []);

        return redirect()
            ->route('admin.staff.show', $staff)
            ->with('status', "Staff member {$staff->full_name} registered as {$staff->staff_number}.");
    }

    public function show(Staff $staff): View
    {
        $staff->load('users');

        return view('admin.staff.show', compact('staff'));
    }

    public function edit(Staff $staff): View
    {
        $staff->load('users');

        $users = User::active()
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        return view('admin.staff.edit', compact('staff', 'users'));
    }

    public function update(UpdateStaffRequest $request, Staff $staff): RedirectResponse
    {
        $data = $request->validated();

        $staff->update($data);

        $staff->users()->sync($data['user_ids'] ?? []);

        return redirect()
            ->route('admin.staff.show', $staff)
            ->with('status', "Staff member {$staff->full_name} updated.");
    }

    public function destroy(Staff $staff): RedirectResponse
    {
        $this->authorize('delete', $staff);

        $name = $staff->full_name;
        $staff->delete();

        return redirect()
            ->route('admin.staff.index')
            ->with('status', "Staff member {$name} deleted.");
    }

    public function restore(int $id): RedirectResponse
    {
        $staff = Staff::onlyTrashed()->findOrFail($id);
        $this->authorize('restore', $staff);

        $staff->restore();

        return redirect()
            ->route('admin.staff.show', $staff)
            ->with('status', "Staff member {$staff->full_name} restored.");
    }

    public function activate(Staff $staff): RedirectResponse
    {
        $this->authorize('update', $staff);

        $staff->update(['status' => 'active']);

        return back()->with('status', "Staff member {$staff->full_name} activated.");
    }

    public function suspend(Request $request, Staff $staff): RedirectResponse
    {
        $this->authorize('update', $staff);

        $request->validate([
            'reason' => ['required', 'string', 'max:255'],
        ]);

        $staff->update([
            'status' => 'suspended',
            'notes' => trim(($staff->notes ?? '') . "\n\n[suspended " . now()->toDateString() . "] " . $request->input('reason')),
        ]);

        return back()->with('status', "Staff member {$staff->full_name} suspended.");
    }
}