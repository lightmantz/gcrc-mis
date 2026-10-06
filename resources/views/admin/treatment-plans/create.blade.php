@extends('gentelella::page')

@section('title', 'New Treatment Plan')
@section('page_key', 'treatment-plans')

@section('content')
    <x-gentelella::page-header title="New Treatment Plan" pretitle="Clinical" />

    <x-gentelella::card>
        <form method="POST" action="{{ route('admin.treatment-plans.store') }}">
            @csrf

            @include('admin.treatment-plans.partials._form', [
                'plan' => null,
                'child' => $child ?? null,
                'children' => $children,
                'staff' => $staff,
                'diagnoses' => $diagnoses ?? collect(),
            ])

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Create Draft Plan</button>
                <a href="{{ route('admin.treatment-plans.index') }}" class="btn btn-outline">Cancel</a>
            </div>
        </form>
    </x-gentelella::card>
@endsection