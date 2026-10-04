@extends('gentelella::page')

@section('title', 'Register Staff')
@section('page_key', 'staff')

@section('content')
    <x-gentelella::page-header title="Register Staff" pretitle="Operations" />

    <x-gentelella::card>
        <form method="POST" action="{{ route('admin.staff.store') }}">
            @csrf
            @include('admin.staff.partials._form', ['staff' => new \App\Models\Staff()])

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Register Staff Member</button>
                <a href="{{ route('admin.staff.index') }}" class="btn btn-outline">Cancel</a>
            </div>
        </form>
    </x-gentelella::card>
@endsection