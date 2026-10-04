@extends('gentelella::page')

@section('title', $role->name)
@section('page_key', 'roles')

@section('content')
    <x-gentelella::page-header title="{{ $role->name }}" pretitle="Role">
        <x-slot:actions>
            @can('update', $role)
                <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-primary">
                    Edit Permissions
                </a>
            @endcan
        </x-slot:actions>
    </x-gentelella::page-header>

    <div class="row col-4-8">
        <div>
            <x-gentelella::card title="Summary">
                <dl style="display: grid; grid-template-columns: auto 1fr; gap: 8px 16px; font-size: 13px;">
                    <dt style="color: var(--text-muted);">Role name</dt>
                    <dd>{{ $role->name }}</dd>

                    <dt style="color: var(--text-muted);">Users assigned</dt>
                    <dd>{{ $userCount }}</dd>

                    <dt style="color: var(--text-muted);">Permissions granted</dt>
                    <dd>
                        @if ($role->name === 'System Administrator')
                            <span style="color: var(--text-muted);">All (protected)</span>
                        @else
                            {{ $role->permissions->count() }}
                        @endif
                    </dd>
                </dl>

                @if ($role->name === 'System Administrator')
                    <div class="alert alert-warning" style="margin-top: 16px;">
                        <div class="alert-body">
                            The <strong>System Administrator</strong> role is protected and cannot be
                            edited or deleted through this interface. Its permissions are managed
                            through the database seeder.
                        </div>
                    </div>
                @endif
            </x-gentelella::card>
        </div>

        <div>
            <x-gentelella::card title="Permissions">
                @foreach ($groupedPermissions as $module => $group)
                    @if ($group['granted_count'] > 0)
                        <div style="margin-bottom: 16px;">
                            <div style="display: flex; justify-content: space-between; align-items: baseline; padding-bottom: 6px; border-bottom: 1px solid var(--border-color-light); margin-bottom: 8px;">
                                <strong style="font-size: 12px; text-transform: uppercase; letter-spacing: 0.4px; color: var(--text-muted);">
                                    {{ $group['label'] }}
                                </strong>
                                <span style="font-size: 12px; color: var(--text-muted);">
                                    {{ $group['granted_count'] }} / {{ $group['total_count'] }}
                                </span>
                            </div>
                            <div style="display: flex; flex-wrap: wrap; gap: 6px;">
                                @foreach ($group['permissions'] as $perm)
                                    @if ($perm['granted'])
                                        <x-gentelella::badge tone="teal">{{ $perm['action'] }}</x-gentelella::badge>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endforeach
            </x-gentelella::card>
        </div>
    </div>
@endsection