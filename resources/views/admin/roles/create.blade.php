@extends('gentelella::page')

@section('title', 'New Role')
@section('page_key', 'roles')

@section('content')
    <x-gentelella::page-header title="New Role" pretitle="System" />

    <x-gentelella::card>
        <form method="POST" action="{{ route('admin.roles.store') }}">
            @csrf
            @include('admin.roles.partials._form')

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Create Role</button>
                <a href="{{ route('admin.roles.index') }}" class="btn btn-outline">Cancel</a>
            </div>
        </form>
    </x-gentelella::card>
@endsection