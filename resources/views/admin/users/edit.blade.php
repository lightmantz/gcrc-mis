@extends('gentelella::page')

@section('title', 'Edit User')
@section('page_key', 'users')

@section('content')
    <x-gentelella::page-header title="Edit User" pretitle="{{ $user->name }}" />

    <x-gentelella::card>
        <form method="POST" action="{{ route('admin.users.update', $user) }}">
            @csrf
            @method('PUT')
            @include('admin.users.partials._form', ['user' => $user])

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Save Changes</button>
                <a href="{{ route('admin.users.show', $user) }}" class="btn btn-outline">Cancel</a>
            </div>
        </form>
    </x-gentelella::card>
@endsection