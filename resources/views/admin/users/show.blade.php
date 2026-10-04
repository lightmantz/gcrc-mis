@extends('gentelella::page')

@section('title', $user->name)
@section('page_key', 'users')

@section('content')
    <x-gentelella::page-header title="{{ $user->name }}" pretitle="{{ $user->email }}">
        <x-slot:actions>
            @can('update', $user)
                <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary">Edit</a>
            @endcan
        </x-slot:actions>
    </x-gentelella::page-header>

    <div class="row col-8-4">
        <div>
            <x-gentelella::card title="Account Details">
                <div class="form-row" style="gap: 8px 24px;">
                    <div>
                        <div class="form-label">Name</div>
                        <p>{{ $user->name }}</p>
                    </div>
                    <div>
                        <div class="form-label">Email</div>
                        <p>{{ $user->email }}</p>
                    </div>
                    <div>
                        <div class="form-label">Status</div>
                        @if ($user->is_active)
                            <span class="status status-green">Active</span>
                        @else
                            <span class="status status-red">Inactive</span>
                        @endif
                    </div>
                    <div>
                        <div class="form-label">Last Login</div>
                        <p>{{ $user->last_login_at?->diffForHumans() ?? 'Never' }}</p>
                    </div>
                    <div>
                        <div class="form-label">Created</div>
                        <p>{{ $user->created_at->format('d M Y, H:i') }}</p>
                    </div>
                </div>

                <hr class="divider-plain">

                <div class="form-label">Roles</div>
                <div style="display: flex; flex-wrap: wrap; gap: 4px;">
                    @foreach ($user->roles as $role)
                        <x-gentelella::badge tone="teal">{{ $role->name }}</x-gentelella::badge>
                    @endforeach
                </div>
            </x-gentelella::card>

            <x-gentelella::card title="Recent Activity" style="margin-top: 16px;">
                @forelse ($user->audits->take(10) as $audit)
                    <div style="padding: 8px 0; border-bottom: 1px solid var(--border-color-light); font-size: 13px;">
                        <strong>{{ ucfirst($audit->event) }}</strong>
                        <span style="color: var(--text-muted);">
                            on {{ $audit->created_at->format('d M Y, H:i') }}
                        </span>
                        @if ($audit->user)
                            <span style="color: var(--text-muted);">
                                by {{ $audit->user->name }}
                            </span>
                        @endif
                    </div>
                @empty
                    <p style="color: var(--text-muted); font-size: 13px;">No activity recorded yet.</p>
                @endforelse
            </x-gentelella::card>
        </div>

        <div>
            <x-gentelella::card title="Actions">
                @can('deactivate', $user)
                    @if ($user->is_active)
                        <form method="POST" action="{{ route('admin.users.deactivate', $user) }}"
                              onsubmit="return confirm('Deactivate this user?')">
                            @csrf
                            <div class="form-group">
                                <label class="form-label">Reason for deactivation</label>
                                <textarea name="reason" class="form-control" required></textarea>
                            </div>
                            <button class="btn btn-danger">Deactivate</button>
                        </form>
                    @endif
                @endcan

                @can('activate', $user)
                    @if (! $user->is_active)
                        <form method="POST" action="{{ route('admin.users.activate', $user) }}">
                            @csrf
                            <button class="btn btn-success">Activate</button>
                        </form>
                    @endif
                @endcan

                @can('delete', $user)
                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                          onsubmit="return confirm('Soft-delete this user? They can be restored later.')"
                          style="margin-top: 12px;">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger">Delete</button>
                    </form>
                @endcan
            </x-gentelella::card>
        </div>
    </div>
@endsection