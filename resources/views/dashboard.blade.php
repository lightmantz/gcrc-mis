{{-- resources/views/dashboard.blade.php --}}
@extends('gentelella::page')

@section('title', 'Dashboard')
@section('page_key', 'dashboard')
@section('breadcrumb', 'Home > Dashboard')

@section('content')
    <x-gentelella::page-header title="Dashboard" pretitle="Overview" />

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <x-gentelella::card title="Total Children">
            <p class="text-3xl font-bold">{{ $totalChildren ?? 0 }}</p>
        </x-gentelella::card>

        <x-gentelella::card title="Staff">
            <p class="text-3xl font-bold">{{ $totalStaff ?? 0 }}</p>
        </x-gentelella::card>

        <x-gentelella::card title="Appointments Today">
            <p class="text-3xl font-bold">{{ $appointmentsToday ?? 0 }}</p>
        </x-gentelella::card>
    </div>
@endsection