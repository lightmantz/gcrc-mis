@extends('gentelella::page')

@section('title', $staff->full_name)
@section('page_key', 'staff')

@section('content')
    <x-gentelella::page-header title="{{ $staff->full_name }}" pretitle="{{ $staff->staff_number }}">
        <x-slot:actions>
            @can('update', $staff)
                <a href="{{ route('admin.staff.edit', $staff) }}" class="btn btn-primary">Edit</a>
            @endcan
        </x-slot:actions>
    </x-gentelella::page-header>

    <div class="row col-8-4">
        <div>
            <x-gentelella::card title="Employment Details">
                <dl style="display: grid; grid-template-columns: auto 1fr; gap: 8px 16px; font-size: 13px;">
                    <dt style="color: var(--text-muted);">Staff number</dt>
                    <dd class="cell-mono">{{ $staff->staff_number }}</dd>

                    <dt style="color: var(--text-muted);">Category</dt>
                    <dd>{{ $staff->category_label }}</dd>

                    <dt style="color: var(--text-muted);">Department</dt>
                    <dd>{{ $staff->department ?? '—' }}</dd>

                    <dt style="color: var(--text-muted);">Job title</dt>
                    <dd>{{ $staff->job_title ?? '—' }}</dd>

                    <dt style="color: var(--text-muted);">Employment type</dt>
                    <dd>{{ $staff->employment_type_label }}</dd>

                    <dt style="color: var(--text-muted);">Start date</dt>
                    <dd>{{ $staff->employment_start_date->format('d M Y') }}</dd>

                    @if ($staff->employment_end_date)
                        <dt style="color: var(--text-muted);">End date</dt>
                        <dd>{{ $staff->employment_end_date->format('d M Y') }}</dd>
                    @endif

                    <dt style="color: var(--text-muted);">Status</dt>
                    <dd>
                        <x-gentelella::badge :tone="$staff->status_tone">
                            {{ $staff->status_label }}
                        </x-gentelella::badge>
                    </dd>
                </dl>
            </x-gentelella::card>

            <x-gentelella::card title="Contact" style="margin-top: 16px;">
                <dl style="display: grid; grid-template-columns: auto 1fr; gap: 8px 16px; font-size: 13px;">
                    <dt style="color: var(--text-muted);">Phone</dt>
                    <dd>{{ $staff->phone }}</dd>

                    <dt style="color: var(--text-muted);">Email</dt>
                    <dd>{{ $staff->email ?? '—' }}</dd>

                    <dt style="color: var(--text-muted);">Address</dt>
                    <dd style="white-space: pre-line;">{{ $staff->address ?? '—' }}</dd>

                    <dt style="color: var(--text-muted);">Emergency contact</dt>
                    <dd>
                        @if ($staff->emergency_contact_name)
                            {{ $staff->emergency_contact_name }}
                            @if ($staff->emergency_contact_relation)
                                ({{ $staff->emergency_contact_relation }})
                            @endif
                            @if ($staff->emergency_contact_phone)
                                — {{ $staff->emergency_contact_phone }}
                            @endif
                        @else
                            —
                        @endif
                    </dd>
                </dl>
            </x-gentelella::card>
        </div>

        <div>
            <x-gentelella::card title="Login Accounts">
                @forelse ($staff->users as $u)
                    <div style="padding: 6px 0; border-bottom: 1px solid var(--border-color-light);">
                        <a href="{{ route('admin.users.show', $u) }}">{{ $u->name }}</a>
                        <div style="color: var(--text-muted); font-size: 12px;">{{ $u->email }}</div>
                    </div>
                @empty
                    <p style="color: var(--text-muted); font-size: 13px;">No login accounts linked.</p>
                @endforelse
            </x-gentelella::card>

            @can('staff.view_sensitive')
                <x-gentelella::card title="Sensitive Information" style="margin-top: 16px;">
                    <dl style="display: grid; grid-template-columns: auto 1fr; gap: 8px 16px; font-size: 13px;">
                        <dt style="color: var(--text-muted);">Salary</dt>
                        <dd>
                            @if ($staff->salary)
                                {{ number_format((float) $staff->salary, 2) }}
                            @else
                                —
                            @endif
                        </dd>
                    </dl>
                </x-gentelella::card>
            @endcan

            @if ($staff->notes)
                <x-gentelella::card title="Internal Notes" style="margin-top: 16px;">
                    <p style="white-space: pre-line; font-size: 13px;">{{ $staff->notes }}</p>
                </x-gentelella::card>
            @endif

            @can('delete', $staff)
                <x-gentelella::card title="Danger Zone" style="margin-top: 16px;">
                    <form method="POST" action="{{ route('admin.staff.destroy', $staff) }}"
                          onsubmit="return confirm('Soft-delete this staff record? It can be restored later.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete Staff Record</button>
                    </form>
                </x-gentelella::card>
            @endcan
        </div>
    </div>
@endsection