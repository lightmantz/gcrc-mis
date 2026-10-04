@extends('gentelella::page')

@section('title', 'Register User')
@section('page_key', 'users')

@section('content')
    <x-gentelella::page-header title="Register User" pretitle="System" />

    <x-gentelella::card>
        <form method="POST" action="{{ route('admin.users.store') }}">
            @csrf
            @include('admin.users.partials._form')

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Create User</button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline">Cancel</a>
            </div>
        </form>
    </x-gentelella::card>
@endsection