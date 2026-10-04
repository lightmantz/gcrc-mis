@extends('gentelella::page')

@section('title', 'Edit Staff')
@section('page_key', 'staff')

@section('content')
    <x-gentelella::page-header title="Edit Staff" pretitle="{{ $staff->staff_number }} — {{ $staff->full_name }}" />

    <x-gentelella::card>
        <form method="POST" action="{{ route('admin.staff.update', $staff) }}">
            @csrf
            @method('PUT')
            @include('admin.staff.partials._form', ['staff' => $staff])

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Save Changes</button>
                <a href="{{ route('admin.staff.show', $staff) }}" class="btn btn-outline">Cancel</a>
            </div>
        </form>
    </x-gentelella::card>
@endsection