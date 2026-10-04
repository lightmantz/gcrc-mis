@extends('gentelella::page')

@section('title', 'Edit Guardian')
@section('page_key', 'guardians')

@section('content')
    <x-gentelella::page-header title="Edit Guardian" pretitle="{{ $guardian->full_name }}" />

    <x-gentelella::card>
        <form method="POST" action="{{ route('admin.guardians.update', $guardian) }}">
            @csrf
            @method('PUT')
            @include('admin.guardians.partials._form', ['guardian' => $guardian])

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Save Changes</button>
                <a href="{{ route('admin.guardians.show', $guardian) }}" class="btn btn-outline">Cancel</a>
            </div>
        </form>
    </x-gentelella::card>
@endsection