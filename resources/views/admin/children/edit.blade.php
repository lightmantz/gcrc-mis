@extends('gentelella::page')

@section('title', 'Edit Child')
@section('page_key', 'children')

@section('content')
    <x-gentelella::page-header title="Edit Child" pretitle="{{ $child->child_number }} — {{ $child->full_name }}" />

    <x-gentelella::card>
        <form method="POST" action="{{ route('admin.children.update', $child) }}">
            @csrf
            @method('PUT')
            @include('admin.children.partials._form', ['child' => $child])

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Save Changes</button>
                <a href="{{ route('admin.children.show', $child) }}" class="btn btn-outline">Cancel</a>
            </div>
        </form>
    </x-gentelella::card>
@endsection