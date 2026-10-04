@extends('gentelella::page')

@section('title', $child->full_name)
@section('page_key', 'children')

@section('content')
    <x-gentelella::page-header title="{{ $child->full_name }}" pretitle="{{ $child->child_number }}">
        <x-slot:actions>
            @can('update', $child)
                <a href="{{ route('admin.children.edit', $child) }}" class="btn btn-primary">Edit</a>
            @endcan
        </x-slot:actions>
    </x-gentelella::page-header>

    <div class="row col-8-4">
        <div>
            <x-gentelella::card title="Identity">
                <dl style="display: grid; grid-template-columns: auto 1fr; gap: 8px 16px; font-size: 13px;">
                    <dt style="color: var(--text-muted);">Child number</dt>
                    <dd class="cell-mono">{{ $child->child_number }}</dd>

                    <dt style="color: var(--text-muted);">Full name</dt>
                    <dd>{{ $child->full_name }}</dd>

                    @if ($child->preferred_name)
                        <dt style="color: var(--text-muted);">Preferred name</dt>
                        <dd>{{ $child->preferred_name }}</dd>
                    @endif

                    <dt style="color: var(--text-muted);">Date of birth</dt>
                    <dd>
                        {{ $child->date_of_birth->format('d M Y') }}
                        <span style="color: var(--text-muted);">({{ $child->age_display }})</span>
                    </dd>

                    <dt style="color: var(--text-muted);">Gender</dt>
                    <dd>{{ ucfirst($child->gender) }}</dd>

                    <dt style="color: var(--text-muted);">Status</dt>
                    <dd>
                        <x-gentelella::badge :tone="$child->status_tone">
                            {{ $child->status_label }}
                        </x-gentelella::badge>
                    </dd>

                    <dt style="color: var(--text-muted);">Registered</dt>
                    <dd>{{ $child->registration_date->format('d M Y') }}</dd>

                    @if ($child->referred_by)
                        <dt style="color: var(--text-muted);">Referred by</dt>
                        <dd>{{ $child->referred_by }}</dd>
                    @endif
                </dl>
            </x-gentelella::card>

            @can('viewMedical', $child)
                <x-gentelella::card title="Medical Summary" style="margin-top: 16px;">
                    <dl style="display: grid; grid-template-columns: auto 1fr; gap: 8px 16px; font-size: 13px;">
                        <dt style="color: var(--text-muted);">Primary condition</dt>
                        <dd>{{ $child->primary_condition_label }}</dd>

                        <dt style="color: var(--text-muted);">Blood type</dt>
                        <dd>{{ $child->blood_type === 'unknown' ? '—' : $child->blood_type }}</dd>

                        <dt style="color: var(--text-muted);">Allergies</dt>
                        <dd style="white-space: pre-line;">{{ $child->allergies ?: '—' }}</dd>

                        <dt style="color: var(--text-muted);">Chronic conditions</dt>
                        <dd style="white-space: pre-line;">{{ $child->chronic_conditions ?: '—' }}</dd>

                        <dt style="color: var(--text-muted);">Current medications</dt>
                        <dd style="white-space: pre-line;">{{ $child->current_medications ?: '—' }}</dd>

                        <dt style="color: var(--text-muted);">Disability summary</dt>
                        <dd style="white-space: pre-line;">{{ $child->disability_summary ?: '—' }}</dd>
                    </dl>
                </x-gentelella::card>
            @endcan

            <x-gentelella::card title="Special Care Requirements" style="margin-top: 16px;">
                <dl style="display: grid; grid-template-columns: auto 1fr; gap: 8px 16px; font-size: 13px;">
                    <dt style="color: var(--text-muted);">Special care</dt>
                    <dd style="white-space: pre-line;">{{ $child->special_care_requirements ?: '—' }}</dd>

                    <dt style="color: var(--text-muted);">Feeding</dt>
                    <dd style="white-space: pre-line;">{{ $child->feeding_requirements ?: '—' }}</dd>

                    <dt style="color: var(--text-muted);">Mobility</dt>
                    <dd style="white-space: pre-line;">{{ $child->mobility_notes ?: '—' }}</dd>

                    <dt style="color: var(--text-muted);">Communication</dt>
                    <dd style="white-space: pre-line;">{{ $child->communication_notes ?: '—' }}</dd>

                    <dt style="color: var(--text-muted);">Supervision</dt>
                    <dd>
                        @if ($child->requires_constant_supervision)
                            <x-gentelella::badge tone="red">Requires constant supervision</x-gentelella::badge>
                        @else
                            Standard
                        @endif
                    </dd>
                </dl>
            </x-gentelella::card>

            @include('admin.children.partials._guardians', ['child' => $child])
        </div>

        <div>
            <x-gentelella::card title="Contact">
                <dl style="display: grid; grid-template-columns: auto 1fr; gap: 8px 16px; font-size: 13px;">
                    <dt style="color: var(--text-muted);">Phone</dt>
                    <dd>{{ $child->phone ?: '—' }}</dd>

                    <dt style="color: var(--text-muted);">Email</dt>
                    <dd>{{ $child->email ?: '—' }}</dd>

                    <dt style="color: var(--text-muted);">Address</dt>
                    <dd style="white-space: pre-line;">{{ $child->address ?: '—' }}</dd>

                    <dt style="color: var(--text-muted);">District</dt>
                    <dd>{{ $child->district ?: '—' }}</dd>

                    <dt style="color: var(--text-muted);">Region</dt>
                    <dd>{{ $child->region ?: '—' }}</dd>
                </dl>
            </x-gentelella::card>

            @if ($child->notes)
                <x-gentelella::card title="Internal Notes" style="margin-top: 16px;">
                    <p style="white-space: pre-line; font-size: 13px;">{{ $child->notes }}</p>
                </x-gentelella::card>
            @endif

            @can('delete', $child)
                <x-gentelella::card title="Danger Zone" style="margin-top: 16px;">
                    <form method="POST" action="{{ route('admin.children.destroy', $child) }}"
                          onsubmit="return confirm('Soft-delete this child record? It can be restored later.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete Record</button>
                    </form>
                </x-gentelella::card>
            @endcan
        </div>
    </div>
@endsection