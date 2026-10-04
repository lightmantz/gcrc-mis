@extends('gentelella::page')

@section('title', 'Edit Role')
@section('page_key', 'roles')

@section('content')
    <x-gentelella::page-header title="Edit Role" pretitle="{{ $role->name }}" />

    <x-gentelella::card>
        <form method="POST" action="{{ route('admin.roles.update', $role) }}">
            @csrf
            @method('PUT')
            @include('admin.roles.partials._form', ['role' => $role])

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Save Permissions</button>
                <a href="{{ route('admin.roles.show', $role) }}" class="btn btn-outline">Cancel</a>
            </div>
        </form>
    </x-gentelella::card>
@endsection