@extends('gentelella::page')

@section('title', 'Edit Treatment Plan')
@section('page_key', 'treatment-plans')

@section('content')
    <x-gentelella::page-header title="Edit Treatment Plan"
                                pretitle="{{ $plan->discipline_label }} — {{ $plan->child->full_name }}" />

    <x-gentelella::card>
        <form method="POST" action="{{ route('admin.treatment-plans.update', $plan) }}">
            @csrf
            @method('PUT')

            @include('admin.treatment-plans.partials._form', [
                'plan' => $plan,
                'children' => collect(),
                'staff' => $staff,
                'diagnoses' => $diagnoses,
            ])

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Save Changes</button>
                <a href="{{ route('admin.treatment-plans.show', $plan) }}" class="btn btn-outline">Cancel</a>
            </div>
        </form>
    </x-gentelella::card>
@endsection