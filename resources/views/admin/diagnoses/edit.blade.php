@extends('gentelella::page')

@section('title', 'Edit Diagnosis')
@section('page_key', 'diagnoses')

@section('content')
    <x-gentelella::page-header title="Edit Diagnosis"
                                pretitle="{{ $diagnosis->display_label }} — {{ $diagnosis->child->full_name }}" />

    @if (! $diagnosis->isActive())
        <div class="alert alert-warning" style="margin-bottom: 16px;">
            <div class="alert-body">
                This diagnosis is <strong>{{ $diagnosis->status_label }}</strong>.
                Only active diagnoses can be edited.
            </div>
        </div>
    @endif

    <x-gentelella::card>
        <form method="POST" action="{{ route('admin.diagnoses.update', $diagnosis) }}">
            @csrf
            @method('PUT')

            @include('admin.diagnoses.partials._form', [
                'diagnosis' => $diagnosis,
                'staff' => $staff,
                'vocabulary' => $vocabulary,
            ])

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Save Changes</button>
                <a href="{{ route('admin.diagnoses.show', $diagnosis) }}" class="btn btn-outline">Cancel</a>
            </div>
        </form>
    </x-gentelella::card>
@endsection