@extends('gentelella::page')

@section('title', 'Edit Assessment')
@section('page_key', 'assessments')

@section('content')
    <x-gentelella::page-header title="Edit Draft Assessment"
                                pretitle="{{ $assessment->type_label }} — {{ $assessment->child->full_name }}" />

    <x-gentelella::card>
        <form method="POST" action="{{ route('admin.assessments.update', $assessment) }}">
            @csrf
            @method('PUT')

            @include('admin.assessments.partials._envelope', [
                'assessment' => $assessment,
                'children' => collect(),
                'staff' => $staff,
                'types' => [],
                'showTypeSelect' => false,
                'showStaffSelect' => true,
            ])

            @include('admin.assessments.partials._fields', [
                'type' => $type,
                'findings' => old('findings', $assessment->findings ?? []),
            ])

            <div class="form-group" style="margin-top: 24px;">
                <label class="form-label" for="summary">Summary</label>
                <textarea id="summary" name="summary" class="form-control" rows="3">{{ old('summary', $assessment->summary) }}</textarea>
            </div>

            <div class="form-group">
                <label class="form-label" for="recommendations">Recommendations</label>
                <textarea id="recommendations" name="recommendations" class="form-control" rows="3">{{ old('recommendations', $assessment->recommendations) }}</textarea>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Save Draft</button>
                <a href="{{ route('admin.assessments.show', $assessment) }}" class="btn btn-outline">Cancel</a>
            </div>
        </form>
    </x-gentelella::card>
@endsection