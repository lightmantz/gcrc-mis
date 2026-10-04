@extends('gentelella::page')

@section('title', 'Register Child')
@section('page_key', 'children')

@section('content')
    <x-gentelella::page-header title="Register Child" pretitle="Registry" />

    <x-gentelella::card>
        <form method="POST" action="{{ route('admin.children.store') }}">
            @csrf
            @include('admin.children.partials._form', ['child' => $child])

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Register Child</button>
                <a href="{{ route('admin.children.index') }}" class="btn btn-outline">Cancel</a>
            </div>
        </form>
    </x-gentelella::card>
@endsection