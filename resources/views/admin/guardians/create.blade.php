@extends('gentelella::page')

@section('title', 'Register Guardian')
@section('page_key', 'guardians')

@section('content')
    <x-gentelella::page-header title="Register Guardian" pretitle="Registry" />

    <x-gentelella::card>
        <form method="POST" action="{{ route('admin.guardians.store') }}">
            @csrf
            @include('admin.guardians.partials._form', ['guardian' => $guardian])

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Register Guardian</button>
                <a href="{{ route('admin.guardians.index') }}" class="btn btn-outline">Cancel</a>
            </div>
        </form>
    </x-gentelella::card>
@endsection