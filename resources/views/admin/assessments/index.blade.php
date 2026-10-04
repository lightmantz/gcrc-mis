@extends('gentelella::page')

@section('title', 'Assessments')
@section('page_key', 'assessments')

@section('content')
    <x-gentelella::page-header title="Assessments" pretitle="Clinical">
        <x-slot:actions>
            @can('assessments.create')
                <a href="{{ route('admin.assessments.create') }}" class="btn btn-primary">
                    New Assessment
                </a>
            @endcan
        </x-slot:actions>
    </x-gentelella::page-header>

    <x-gentelella::card flush>
        <div class="card-body" style="border-bottom: 1px solid var(--border-color-light);">
            <form method="GET" class="form-row cols-3" style="align-items: end;">
                <div class="form-group">
                    <label class="form-label" for="type">Type</label>
                    <select id="type" name="type" class="form-control">
                        <option value="">All types</option>
                        @foreach (\App\Assessments\AssessmentTypeRegistry::options() as $key => $label)
                            <option value="{{ $key }}" @selected(request('type') === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="status">Status</label>
                    <select id="status" name="status" class="form-control">
                        <option value="">All</option>
                        <option value="draft" @selected(request('status') === 'draft')>Draft</option>
                        <option value="finalized" @selected(request('status') === 'finalized')>Finalized</option>
                    </select>
                </div>

                <div class="form-actions" style="margin: 0; padding: 0; border: none;">
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <a href="{{ route('admin.assessments.index') }}" class="btn btn-outline">Reset</a>
                </div>
            </form>
        </div>

        <x-gentelella::table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Child</th>
                    <th>Type</th>
                    <th>Assessor</th>
                    <th>Status</th>
                    <th style="text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($assessments as $assessment)
                    <tr>
                        <td>{{ $assessment->assessment_date->format('d M Y') }}</td>
                        <td>
                            <a href="{{ route('admin.children.show', $assessment->child) }}">
                                {{ $assessment->child->full_name }}
                            </a>
                        </td>
                        <td>{{ $assessment->type_label }}</td>
                        <td>{{ $assessment->assessor->full_name ?? '—' }}</td>
                        <td>
                            <x-gentelella::badge :tone="$assessment->status_tone">
                                {{ $assessment->status_label }}
                            </x-gentelella::badge>
                        </td>
                        <td style="text-align: right; white-space: nowrap;">
                            <a href="{{ route('admin.assessments.show', $assessment) }}"
                               class="btn btn-sm btn-outline">View</a>
                            @can('update', $assessment)
                                <a href="{{ route('admin.assessments.edit', $assessment) }}"
                                   class="btn btn-sm btn-outline">Edit</a>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 32px 16px; color: var(--text-muted);">
                            No assessments recorded yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </x-gentelella::table>

        @if ($assessments->hasPages())
            <div style="padding: 12px 16px; border-top: 1px solid var(--border-color-light);">
                {{ $assessments->links() }}
            </div>
        @endif
    </x-gentelella::card>
@endsection