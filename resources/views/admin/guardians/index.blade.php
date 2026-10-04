@extends('gentelella::page')

@section('title', 'Guardians')
@section('page_key', 'guardians')

@section('content')
    <x-gentelella::page-header title="Guardians" pretitle="Registry">
        <x-slot:actions>
            @can('guardians.create')
                <a href="{{ route('admin.guardians.create') }}" class="btn btn-primary">
                    Register Guardian
                </a>
            @endcan
        </x-slot:actions>
    </x-gentelella::page-header>

    <x-gentelella::card flush>
        <div class="card-body" style="border-bottom: 1px solid var(--border-color-light);">
            <form method="GET" class="form-row" style="align-items: end;">
                <div class="form-group" style="max-width: 400px;">
                    <label class="form-label" for="search">Search</label>
                    <input type="text" id="search" name="search" value="{{ request('search') }}"
                           class="form-control" placeholder="Name, phone, or email">
                </div>

                <div class="form-actions" style="margin: 0; padding: 0; border: none;">
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <a href="{{ route('admin.guardians.index') }}" class="btn btn-outline">Reset</a>
                </div>
            </form>
        </div>

        <x-gentelella::table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Children</th>
                    <th style="text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($guardians as $guardian)
                    <tr>
                        <td class="cell-strong">
                            <a href="{{ route('admin.guardians.show', $guardian) }}">
                                {{ $guardian->full_name }}
                            </a>
                        </td>
                        <td>{{ $guardian->phone }}</td>
                        <td>{{ $guardian->email ?? '—' }}</td>
                        <td>
                            @if ($guardian->children_count === 0)
                                <span style="color: var(--text-muted);">None</span>
                            @else
                                <x-gentelella::badge tone="teal">{{ $guardian->children_count }}</x-gentelella::badge>
                            @endif
                        </td>
                        <td style="text-align: right; white-space: nowrap;">
                            <a href="{{ route('admin.guardians.show', $guardian) }}" class="btn btn-sm btn-outline">View</a>
                            @can('update', $guardian)
                                <a href="{{ route('admin.guardians.edit', $guardian) }}" class="btn btn-sm btn-outline">Edit</a>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 32px 16px; color: var(--text-muted);">
                            No guardians found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </x-gentelella::table>

        @if ($guardians->hasPages())
            <div style="padding: 12px 16px; border-top: 1px solid var(--border-color-light);">
                {{ $guardians->links() }}
            </div>
        @endif
    </x-gentelella::card>
@endsection