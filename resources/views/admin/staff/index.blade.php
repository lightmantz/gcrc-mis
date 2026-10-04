@extends('gentelella::page')

@section('title', 'Staff')
@section('page_key', 'staff')

@section('content')
    <x-gentelella::page-header title="Staff" pretitle="Operations">
        <x-slot:actions>
            @can('staff.create')
                <a href="{{ route('admin.staff.create') }}" class="btn btn-primary">
                    Register Staff
                </a>
            @endcan
        </x-slot:actions>
    </x-gentelella::page-header>

    <x-gentelella::card flush>
        <div class="card-body" style="border-bottom: 1px solid var(--border-color-light);">
            <form method="GET" class="form-row cols-3" style="align-items: end;">
                <div class="form-group">
                    <label class="form-label" for="search">Search</label>
                    <input type="text" id="search" name="search" value="{{ request('search') }}"
                           class="form-control" placeholder="Name, staff number, phone">
                </div>

                <div class="form-group">
                    <label class="form-label" for="category">Category</label>
                    <select id="category" name="category" class="form-control">
                        <option value="">All categories</option>
                        @foreach (['management','clinical','therapy','education','admin','support'] as $c)
                            <option value="{{ $c }}" @selected(request('category') === $c)>{{ ucfirst($c) }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="department">Department</label>
                    <select id="department" name="department" class="form-control">
                        <option value="">All departments</option>
                        @foreach ($departments as $d)
                            <option value="{{ $d }}" @selected(request('department') === $d)>{{ $d }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="status">Status</label>
                    <select id="status" name="status" class="form-control">
                        <option value="">All</option>
                        @foreach (['active','on_leave','suspended','terminated'] as $s)
                            <option value="{{ $s }}" @selected(request('status') === $s)>{{ ucfirst(str_replace('_',' ', $s)) }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-actions" style="margin: 0; padding: 0; border: none;">
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <a href="{{ route('admin.staff.index') }}" class="btn btn-outline">Reset</a>
                </div>
            </form>
        </div>

        <x-gentelella::table>
            <thead>
                <tr>
                    <th>Staff #</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Department</th>
                    <th>Status</th>
                    <th>Account</th>
                    <th style="text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($staff as $member)
                    <tr>
                        <td class="cell-mono">{{ $member->staff_number }}</td>
                        <td class="cell-strong">
                            <a href="{{ route('admin.staff.show', $member) }}">
                                {{ $member->full_name }}
                            </a>
                        </td>
                        <td>{{ $member->category_label }}</td>
                        <td>{{ $member->department ?? '—' }}</td>
                        <td>
                            <x-gentelella::badge :tone="$member->status_tone">
                                {{ $member->status_label }}
                            </x-gentelella::badge>
                        </td>
                        <td>
                            @if ($member->users->isEmpty())
                                <span style="color: var(--text-muted);">No account</span>
                            @else
                                @foreach ($member->users as $u)
                                    <x-gentelella::badge tone="teal">{{ $u->name }}</x-gentelella::badge>
                                @endforeach
                            @endif
                        </td>
                        <td style="text-align: right; white-space: nowrap;">
                            <a href="{{ route('admin.staff.show', $member) }}" class="btn btn-sm btn-outline">View</a>
                            @can('update', $member)
                                <a href="{{ route('admin.staff.edit', $member) }}" class="btn btn-sm btn-outline">Edit</a>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 32px 16px; color: var(--text-muted);">
                            No staff records found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </x-gentelella::table>

        @if ($staff->hasPages())
            <div style="padding: 12px 16px; border-top: 1px solid var(--border-color-light);">
                {{ $staff->links() }}
            </div>
        @endif
    </x-gentelella::card>
@endsection