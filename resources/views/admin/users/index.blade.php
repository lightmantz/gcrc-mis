]@extends('gentelella::page')

@section('title', 'User Accounts')
@section('page_key', 'users')

@section('content')
    <x-gentelella::page-header title="User Accounts" pretitle="System">
        <x-slot:actions>
            @can('users.create')
                <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                    Register User
                </a>
            @endcan
        </x-slot:actions>
    </x-gentelella::page-header>

    <x-gentelella::card flush>
        {{-- Filters --}}
        <div class="card-body" style="border-bottom: 1px solid var(--border-color-light);">
            <form method="GET" class="form-row cols-3" style="align-items: end;">
                <div class="form-group">
                    <label class="form-label" for="search">Search</label>
                    <input type="text" id="search" name="search" value="{{ request('search') }}"
                           class="form-control" placeholder="Name or email">
                </div>

                <div class="form-group">
                    <label class="form-label" for="role">Role</label>
                    <select id="role" name="role" class="form-control">
                        <option value="">All roles</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role }}" @selected(request('role') === $role)>{{ $role }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="status">Status</label>
                    <select id="status" name="status" class="form-control">
                        <option value="">All</option>
                        <option value="active" @selected(request('status') === 'active')>Active</option>
                        <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
                    </select>
                </div>

                <div class="form-actions" style="margin: 0; padding: 0; border: none;">
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline">Reset</a>
                </div>
            </form>
        </div>

        {{-- Table --}}
        <x-gentelella::table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Roles</th>
                    <th>Status</th>
                    <th>Last Login</th>
                    <th style="text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $user)
                    <tr>
                        <td class="cell-strong">
                            <a href="{{ route('admin.users.show', $user) }}">{{ $user->name }}</a>
                        </td>
                        <td>{{ $user->email }}</td>
                        <td>
                            @foreach ($user->roles as $role)
                                <x-gentelella::badge tone="teal">{{ $role->name }}</x-gentelella::badge>
                            @endforeach
                        </td>
                        <td>
                            @if ($user->is_active)
                                <span class="status status-green">Active</span>
                            @else
                                <span class="status status-red">Inactive</span>
                            @endif
                        </td>
                        <td>{{ $user->last_login_at?->diffForHumans() ?? 'Never' }}</td>
                        <td style="text-align: right; white-space: nowrap;">
                            {{-- View --}}
                            @can('view', $user)
                                <a href="{{ route('admin.users.show', $user) }}"
                                   class="btn btn-sm btn-outline">
                                    View
                                </a>
                            @endcan

                            {{-- Edit --}}
                            @can('update', $user)
                                <a href="{{ route('admin.users.edit', $user) }}"
                                   class="btn btn-sm btn-outline">
                                    Edit
                                </a>
                            @endcan

                            {{-- Delete --}}
                            @can('delete', $user)
                                <form method="POST"
                                      action="{{ route('admin.users.destroy', $user) }}"
                                      style="display: inline;"
                                      onsubmit="return confirm('Delete {{ $user->name }}? This soft-deletes the account and it can be restored later.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        Delete
                                    </button>
                                </form>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 32px 16px; color: var(--text-muted);">
                            No users found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </x-gentelella::table>

        {{-- Pagination --}}
        @if ($users->hasPages())
            <div style="padding: 12px 16px; border-top: 1px solid var(--border-color-light);">
                {{ $users->links() }}
            </div>
        @endif
    </x-gentelella::card>
@endsection