@extends('gentelella::page')

@section('title', 'Login History')
@section('page_key', 'logins')

@section('content')
    <x-gentelella::page-header title="Login History" pretitle="System">
        <x-slot:actions>
            @can('login_history.delete')
                <form method="POST" action="{{ route('admin.login-history.purge') }}"
                      style="display: inline-flex; gap: 8px; align-items: center;"
                      onsubmit="return confirm('Delete login records older than the selected number of days?');">
                    @csrf
                    <input type="number" name="days" value="90" min="1" max="3650"
                           class="form-control" style="width: 100px;" title="Days to keep">
                    <button type="submit" class="btn btn-outline">Purge older than…</button>
                </form>
            @endcan
        </x-slot:actions>
    </x-gentelella::page-header>

    <x-gentelella::card flush>
        {{-- Filters --}}
        <div class="card-body" style="border-bottom: 1px solid var(--border-color-light);">
            <form method="GET" class="form-row cols-3" style="align-items: end;">
                <div class="form-group">
                    <label class="form-label" for="user">User</label>
                    <select id="user" name="user" class="form-control">
                        <option value="">All users</option>
                        @foreach ($users as $u)
                            <option value="{{ $u['id'] }}" @selected((string) request('user') === (string) $u['id'])>
                                {{ $u['name'] }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="outcome">Outcome</label>
                    <select id="outcome" name="outcome" class="form-control">
                        <option value="">All</option>
                        <option value="successful" @selected(request('outcome') === 'successful')>Successful</option>
                        <option value="failed" @selected(request('outcome') === 'failed')>Failed</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="ip">IP Address</label>
                    <input type="text" id="ip" name="ip" value="{{ request('ip') }}"
                           class="form-control" placeholder="Partial match">
                </div>

                <div class="form-group">
                    <label class="form-label" for="from">From</label>
                    <input type="date" id="from" name="from" value="{{ request('from') }}"
                           class="form-control">
                </div>

                <div class="form-group">
                    <label class="form-label" for="to">To</label>
                    <input type="date" id="to" name="to" value="{{ request('to') }}"
                           class="form-control">
                </div>

                <div class="form-actions" style="margin: 0; padding: 0; border: none;">
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <a href="{{ route('admin.login-history.index') }}" class="btn btn-outline">Reset</a>
                </div>
            </form>
        </div>

        {{-- Table --}}
        <x-gentelella::table>
            <thead>
                <tr>
                    <th>When</th>
                    <th>Email</th>
                    <th>User</th>
                    <th>Outcome</th>
                    <th>IP</th>
                    <th>Session</th>
                    <th style="text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($entries as $entry)
                    <tr>
                        <td style="white-space: nowrap;">
                            {{ $entry->logged_in_at?->format('d M Y, H:i') ?? '—' }}
                        </td>
                        <td>{{ $entry->email }}</td>
                        <td>
                            @if ($entry->user)
                                <a href="{{ route('admin.users.show', $entry->user) }}">
                                    {{ $entry->user->name }}
                                </a>
                            @else
                                <span style="color: var(--text-muted);">—</span>
                            @endif
                        </td>
                        <td>
                            @if ($entry->successful)
                                <span class="status status-green">Success</span>
                            @else
                                <span class="status status-red">Failed</span>
                                @if ($entry->failure_reason)
                                    <span style="color: var(--text-muted); font-size: 11.5px; margin-left: 4px;">
                                        ({{ $entry->failure_reason }})
                                    </span>
                                @endif
                            @endif
                        </td>
                        <td class="cell-mono">{{ $entry->ip_address }}</td>
                        <td>
                            @if ($entry->successful)
                                @if ($entry->logged_out_at)
                                    {{ $entry->logged_in_at->diffForHumans($entry->logged_out_at, true) }}
                                    <span style="color: var(--text-muted); font-size: 11.5px;">(closed)</span>
                                @else
                                    <span class="status status-blue">Open</span>
                                @endif
                            @else
                                —
                            @endif
                        </td>
                        <td style="text-align: right; white-space: nowrap;">
                            <a href="{{ route('admin.login-history.show', $entry) }}"
                               class="btn btn-sm btn-outline">
                                View
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 32px 16px; color: var(--text-muted);">
                            No login records found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </x-gentelella::table>

        @if ($entries->hasPages())
            <div style="padding: 12px 16px; border-top: 1px solid var(--border-color-light);">
                {{ $entries->links() }}
            </div>
        @endif
    </x-gentelella::card>
@endsection