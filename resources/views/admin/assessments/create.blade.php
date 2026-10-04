@extends('gentelella::page')

@section('title', 'New Assessment')
@section('page_key', 'assessments')

@section('content')
    <x-gentelella::page-header title="New Assessment" pretitle="Clinical" />

    <x-gentelella::card>
        <form method="POST" action="{{ route('admin.assessments.store') }}">
            @csrf

            @include('admin.assessments.partials._envelope', [
                'assessment' => null,
                'child' => $child ?? null,
                'children' => $children,
                'staff' => $staff,
                'types' => \App\Assessments\AssessmentTypeRegistry::options(),
                'showTypeSelect' => true,
                'showStaffSelect' => false,
            ])

            @include('admin.assessments.partials._fields', [
                'type' => null,  // resolved client-side; server-side rendering happens on validation failure
                'findings' => old('findings', []),
            ])

            <div class="form-group" style="margin-top: 24px;">
                <label class="form-label" for="summary">Summary</label>
                <textarea id="summary" name="summary" class="form-control" rows="3">{{ old('summary') }}</textarea>
                <p class="form-help">Brief narrative summary of the assessment findings.</p>
            </div>

            <div class="form-group">
                <label class="form-label" for="recommendations">Recommendations</label>
                <textarea id="recommendations" name="recommendations" class="form-control" rows="3">{{ old('recommendations') }}</textarea>
                <p class="form-help">Clinical recommendations arising from this assessment.</p>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Create Draft</button>
                <a href="{{ route('admin.assessments.index') }}" class="btn btn-outline">Cancel</a>
            </div>
        </form>
    </x-gentelella::card>

    @include('admin.assessments.partials._type_script', ['types' => \App\Assessments\AssessmentTypeRegistry::all()])
@endsection