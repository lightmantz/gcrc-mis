<?php

namespace App\Http\Controllers\Admin;

use App\Support\PermissionGrouper;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    /**
     * Roles seeded by RoleSeeder. Names for these are locked.
     */
    private const SEEDED_ROLES = [
        'System Administrator',
        'Center Manager/Director',
        'Clinical/Medical Staff',
        'Physiotherapist',
        'Vocational Training Expert',
        'Occupational Therapist',
        'Speech/Language Therapist',
        'Nurse',
        'Teacher',
        'Head Teacher / Education Coordinator',
        'Special Education Teacher',
        'Teaching Assistant',
        'School Records Officer',
        'School Administrator',
        'Examination/Assessment Officer',
        'Social Worker',
        'Psychologist/Counselor',
        'Matron/Patron',
        'Reception/Records Officer',
        'Pharmacy/Store Officer',
        'Finance/Accounts Officer',
        'Data/Reporting Officer',
    ];

    public function index(): View
    {
        $roles = Role::withCount(['users', 'permissions'])
            ->orderBy('name')
            ->get();

        return view('admin.roles.index', compact('roles'));
    }

    public function show(Role $role): View
    {
        $role->load('permissions');

        // Group permissions by module prefix (before the first dot)
        $granted = $role->permissions->pluck('name')->all();
        $groupedPermissions = PermissionGrouper::group($granted);

        $userCount = $role->users()->count();

        return view('admin.roles.show', compact('role', 'groupedPermissions', 'userCount'));
    }

    public function create(): View
    {
        $granted = [];
        $cloneFrom = null;

        // Preload permissions if cloning
        if ($requested = old('clone_from')) {
            $base = Role::findByName($requested);
            if ($base) {
                $granted = $base->permissions->pluck('name')->all();
                $cloneFrom = $requested;
            }
        }

        $groupedPermissions = PermissionGrouper::group($granted);

        // Exclude System Administrator from clone options
        $clonableRoles = Role::where('name', '!=', 'System Administrator')
            ->orderBy('name')
            ->pluck('name');

        return view('admin.roles.create', compact('groupedPermissions', 'clonableRoles', 'cloneFrom'));
    }

    public function store(StoreRoleRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $role = Role::create([
            'name' => $data['name'],
            'guard_name' => 'web',
        ]);

        $role->syncPermissions($data['permissions'] ?? []);

        return redirect()
            ->route('admin.roles.show', $role)
            ->with('status', "Role \"{$role->name}\" created.");
    }

    public function edit(Role $role): View
    {
        $this->authorize('update', $role);

        $granted = $role->permissions->pluck('name')->all();
        $groupedPermissions = PermissionGrouper::group($granted);

        return view('admin.roles.edit', compact('role', 'groupedPermissions'));
    }

    public function update(UpdateRoleRequest $request, Role $role): RedirectResponse
    {
        $this->authorize('update', $role);

        $data = $request->validated();

        // Only allow renaming custom (non-seeded) roles.
        if (! in_array($role->name, self::SEEDED_ROLES, true) && $data['name'] !== $role->name) {
            $role->update(['name' => $data['name']]);
        }

        $role->syncPermissions($data['permissions'] ?? []);

        return redirect()
            ->route('admin.roles.show', $role)
            ->with('status', "Role \"{$role->name}\" updated.");
    }

    public function destroy(Role $role): RedirectResponse
    {
        $this->authorize('delete', $role);

        $name = $role->name;
        $role->delete();

        return redirect()
            ->route('admin.roles.index')
            ->with('status', "Role \"{$name}\" deleted.");
    }
}