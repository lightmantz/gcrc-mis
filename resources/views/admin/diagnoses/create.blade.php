@extends('gentelella::page')

@section('title', 'Record Diagnosis')
@section('page_key', 'diagnoses')

@section('content')
    <x-gentelella::page-header title="Record Diagnosis" pretitle="Clinical" />

    <x-gentelella::card>
        <form method="POST" action="{{ route('admin.diagnoses.store') }}">
            @csrf

            @include('admin.diagnoses.partials._form', [
                'diagnosis' => null,
                'child' => $child ?? null,
                'children' => $children,
                'staff' => $staff,
                'vocabulary' => $vocabulary,
            ])

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Record Diagnosis</button>
                <a href="{{ route('admin.diagnoses.index') }}" class="btn btn-outline">Cancel</a>
            </div>
        </form>
    </x-gentelella::card>
@endsection