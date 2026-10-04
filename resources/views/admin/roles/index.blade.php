@extends('gentelella::page')

@section('title', 'Roles & Permissions')
@section('page_key', 'roles')

@section('content')
    <x-gentelella::page-header title="Roles & Permissions" pretitle="System">
        <x-slot:actions>
            @can('roles.create')
                <a href="{{ route('admin.roles.create') }}" class="btn btn-primary">
                    New Role
                </a>
            @endcan
        </x-slot:actions>
    </x-gentelella::page-header>

    <x-gentelella::card flush>
        <x-gentelella::table>
            <thead>
                <tr>
                    <th>Role</th>
                    <th style="text-align: right;">Users</th>
                    <th style="text-align: right;">Permissions</th>
                    <th style="text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($roles as $role)
                    <tr>
                        <td class="cell-strong">
                            <a href="{{ route('admin.roles.show', $role) }}">
                                {{ $role->name }}
                            </a>
                            @if ($role->name === 'System Administrator')
                                <x-gentelella::badge tone="red">System</x-gentelella::badge>
                            @endif
                        </td>
                        <td style="text-align: right;">{{ $role->users_count }}</td>
                        <td style="text-align: right;">
                            @if ($role->name === 'System Administrator')
                                <span style="color: var(--text-muted);">All</span>
                            @else
                                {{ $role->permissions_count }}
                            @endif
                        </td>
                        <td style="text-align: right; white-space: nowrap;">
                            <a href="{{ route('admin.roles.show', $role) }}"
                               class="btn btn-sm btn-outline">
                                View
                            </a>
                            @can('update', $role)
                                <a href="{{ route('admin.roles.edit', $role) }}"
                                   class="btn btn-sm btn-outline">
                                    Edit
                                </a>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="text-align: center; padding: 32px 16px; color: var(--text-muted);">
                            No roles found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </x-gentelella::table>
    </x-gentelella::card>
@endsection