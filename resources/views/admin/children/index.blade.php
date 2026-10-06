@extends('gentelella::page')

@section('title', 'Children')
@section('page_key', 'children')

@section('content')
    <x-gentelella::page-header title="Children" pretitle="Registry">
        <x-slot:actions>
            @can('children.create')
                <a href="{{ route('admin.children.create') }}" class="btn btn-primary">
                    Register Child
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
                           class="form-control" placeholder="Name, child number, phone">
                </div>

                <div class="form-group">
                    <label class="form-label" for="condition">Primary condition</label>
                    <select id="condition" name="condition" class="form-control">
                        <option value="">All conditions</option>
                        @foreach ([
                            'cerebral_palsy' => 'Cerebral Palsy',
                            'down_syndrome' => 'Down Syndrome',
                            'autism_spectrum' => 'Autism Spectrum',
                            'intellectual_disability' => 'Intellectual Disability',
                            'physical_disability' => 'Physical Disability',
                            'hearing_impairment' => 'Hearing Impairment',
                            'visual_impairment' => 'Visual Impairment',
                            'speech_language_disorder' => 'Speech / Language Disorder',
                            'learning_disability' => 'Learning Disability',
                            'multiple_disabilities' => 'Multiple Disabilities',
                            'other' => 'Other',
                        ] as $v => $l)
                            <option value="{{ $v }}" @selected(request('condition') === $v)>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="gender">Gender</label>
                    <select id="gender" name="gender" class="form-control">
                        <option value="">All</option>
                        <option value="male" @selected(request('gender') === 'male')>Male</option>
                        <option value="female" @selected(request('gender') === 'female')>Female</option>
                        <option value="other" @selected(request('gender') === 'other')>Other</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="status">Status</label>
                    <select id="status" name="status" class="form-control">
                        <option value="">All</option>
                        @foreach (['active' => 'Active', 'on_hold' => 'On Hold', 'discharged' => 'Discharged', 'deceased' => 'Deceased'] as $v => $l)
                            <option value="{{ $v }}" @selected(request('status') === $v)>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-actions" style="margin: 0; padding: 0; border: none;">
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <a href="{{ route('admin.children.export', request()->query()) }}" class="btn btn-outline">
                        Export CSV
                    </a>
                    <a href="{{ route('admin.children.index') }}" class="btn btn-outline">Reset</a>
                </div>
            </form>
        </div>

        {{-- Table --}}
        <x-gentelella::table>
            <thead>
                <tr>
                    <th>Child #</th>
                    <th>Name</th>
                    <th>Age</th>
                    <th>Gender</th>
                    <th>Primary Condition</th>
                    <th>Status</th>
                    <th style="text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($children as $child)
                    <tr>
                        <td class="cell-mono">{{ $child->child_number }}</td>
                        <td class="cell-strong">
                            <a href="{{ route('admin.children.show', $child) }}">
                                {{ $child->full_name }}
                            </a>
                            @if ($child->preferred_name && $child->preferred_name !== $child->first_name)
                                <span style="color: var(--text-muted); font-size: 11.5px;">
                                    ({{ $child->preferred_name }})
                                </span>
                            @endif
                        </td>
                        <td>{{ $child->age_display }}</td>
                        <td>{{ ucfirst($child->gender) }}</td>
                        <td>
                            @if ($child->primary_condition)
                                <x-gentelella::badge tone="teal">
                                    {{ $child->primary_condition_label }}
                                </x-gentelella::badge>
                            @else
                                <span style="color: var(--text-muted);">—</span>
                            @endif
                        </td>
                        <td>
                            <x-gentelella::badge :tone="$child->status_tone">
                                {{ $child->status_label }}
                            </x-gentelella::badge>
                        </td>
                        <td style="text-align: right; white-space: nowrap;">
                            <a href="{{ route('admin.children.show', $child) }}"
                               class="btn btn-sm btn-outline">View</a>
                            @can('update', $child)
                                <a href="{{ route('admin.children.edit', $child) }}"
                                   class="btn btn-sm btn-outline">Edit</a>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 32px 16px; color: var(--text-muted);">
                            No children registered yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </x-gentelella::table>

        @if ($children->hasPages())
            <div style="padding: 12px 16px; border-top: 1px solid var(--border-color-light);">
                {{ $children->links() }}
            </div>
        @endif
    </x-gentelella::card>
@endsection