@extends('gentelella::page')

@section('title', $guardian->full_name)
@section('page_key', 'guardians')

@section('content')
    <x-gentelella::page-header title="{{ $guardian->full_name }}" pretitle="Guardian">
        <x-slot:actions>
            @can('update', $guardian)
                <a href="{{ route('admin.guardians.edit', $guardian) }}" class="btn btn-primary">Edit</a>
            @endcan
        </x-slot:actions>
    </x-gentelella::page-header>

    <div class="row col-8-4">
        <div>
            <x-gentelella::card title="Contact Details">
                <dl style="display: grid; grid-template-columns: auto 1fr; gap: 8px 16px; font-size: 13px;">
                    <dt style="color: var(--text-muted);">Phone</dt>
                    <dd>{{ $guardian->phone }}</dd>

                    @if ($guardian->alternate_phone)
                        <dt style="color: var(--text-muted);">Alternate phone</dt>
                        <dd>{{ $guardian->alternate_phone }}</dd>
                    @endif

                    <dt style="color: var(--text-muted);">Email</dt>
                    <dd>{{ $guardian->email ?? '—' }}</dd>

                    <dt style="color: var(--text-muted);">Address</dt>
                    <dd style="white-space: pre-line;">{{ $guardian->address ?? '—' }}</dd>

                    <dt style="color: var(--text-muted);">District</dt>
                    <dd>{{ $guardian->district ?? '—' }}</dd>

                    <dt style="color: var(--text-muted);">Region</dt>
                    <dd>{{ $guardian->region ?? '—' }}</dd>
                </dl>
            </x-gentelella::card>

            @if ($guardian->occupation || $guardian->employer || $guardian->education_level)
                <x-gentelella::card title="Background" style="margin-top: 16px;">
                    <dl style="display: grid; grid-template-columns: auto 1fr; gap: 8px 16px; font-size: 13px;">
                        <dt style="color: var(--text-muted);">Occupation</dt>
                        <dd>{{ $guardian->occupation ?? '—' }}</dd>

                        <dt style="color: var(--text-muted);">Employer</dt>
                        <dd>{{ $guardian->employer ?? '—' }}</dd>

                        <dt style="color: var(--text-muted);">Education</dt>
                        <dd>{{ $guardian->education_level ?? '—' }}</dd>
                    </dl>
                </x-gentelella::card>
            @endif

            @if ($guardian->notes)
                <x-gentelella::card title="Internal Notes" style="margin-top: 16px;">
                    <p style="white-space: pre-line; font-size: 13px;">{{ $guardian->notes }}</p>
                </x-gentelella::card>
            @endif
        </div>

        <div>
            <x-gentelella::card title="Linked Children">
                @forelse ($guardian->children as $child)
                    <div style="padding: 8px 0; border-bottom: 1px solid var(--border-color-light);">
                        <a href="{{ route('admin.children.show', $child) }}" class="cell-strong">
                            {{ $child->full_name }}
                        </a>
                        <div style="font-size: 12px; color: var(--text-muted);">
                            <span class="cell-mono">{{ $child->child_number }}</span>
                            —
                            {{ ucfirst($child->pivot->relationship) }}
                            @if ($child->pivot->is_primary)
                                <x-gentelella::badge tone="teal">Primary</x-gentelella::badge>
                            @endif
                        </div>
                    </div>
                @empty
                    <p style="color: var(--text-muted); font-size: 13px;">
                        Not linked to any child yet.
                    </p>
                @endforelse
            </x-gentelella::card>

            @can('delete', $guardian)
                <x-gentelella::card title="Danger Zone" style="margin-top: 16px;">
                    <form method="POST" action="{{ route('admin.guardians.destroy', $guardian) }}"
                          onsubmit="return confirm('Delete this guardian? Only possible when not linked to any child.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete Guardian</button>
                    </form>
                </x-gentelella::card>
            @endcan
        </div>
    </div>
@endsection