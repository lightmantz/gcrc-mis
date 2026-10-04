<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $users = User::query()
            ->with('roles')
            ->when($request->string('search')->toString(), function ($q, $search) {
                $q->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($request->string('role')->toString(), function ($q, $role) {
                $q->role($role);
            })
            ->when($request->has('status'), function ($q) use ($request) {
                $q->where('is_active', $request->string('status')->toString() === 'active');
            })
            ->when($request->boolean('trashed'), function ($q) {
                $q->onlyTrashed();
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $roles = Role::orderBy('name')->pluck('name');

        return view('admin.users.index', compact('users', 'roles'));
    }

    public function create(): View
    {
        $roles = Role::orderBy('name')->pluck('name');

        return view('admin.users.create', compact('roles'));
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'is_active' => $data['is_active'] ?? true,
            'email_verified_at' => now(),
        ]);

        $user->syncRoles($data['roles']);

        return redirect()
            ->route('admin.users.show', $user)
            ->with('status', "User {$user->name} created.");
    }

    public function show(User $user): View
    {
        $user->load('roles', 'audits');

        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user): View
    {
        $this->authorize('update', $user);

        $roles = Role::orderBy('name')->pluck('name');

        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $this->authorize('update', $user);

        $data = $request->validated();

        $user->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'is_active' => $data['is_active'] ?? $user->is_active,
            ...($data['password'] ? ['password' => $data['password']] : []),
        ]);

        $user->syncRoles($data['roles']);

        return redirect()
            ->route('admin.users.show', $user)
            ->with('status', "User {$user->name} updated.");
    }

    public function destroy(User $user): RedirectResponse
    {
        $this->authorize('delete', $user);

        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('status', "User {$user->name} deleted.");
    }

    public function restore(int $id): RedirectResponse
    {
        $user = User::onlyTrashed()->findOrFail($id);

        $this->authorize('restore', $user);

        $user->restore();

        return redirect()
            ->route('admin.users.show', $user)
            ->with('status', "User {$user->name} restored.");
    }

    public function activate(User $user): RedirectResponse
    {
        $this->authorize('activate', $user);

        $user->activate();

        return back()->with('status', "User {$user->name} activated.");
    }

    public function deactivate(Request $request, User $user): RedirectResponse
    {
        $this->authorize('deactivate', $user);

        $request->validate([
            'reason' => ['required', 'string', 'max:255'],
        ]);

        $user->deactivate($request->user(), $request->input('reason'));

        return back()->with('status', "User {$user->name} deactivated.");
    }
}