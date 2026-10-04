@extends('gentelella::page')

@section('title', 'Audit Trail')
@section('page_key', 'audit')

@section('content')
    <x-gentelella::page-header title="Audit Trail" pretitle="System">
        <x-slot:actions>
            <a href="{{ route('admin.audit.export', request()->query()) }}"
               class="btn btn-outline">
                Export CSV
            </a>
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
                    <label class="form-label" for="event">Event</label>
                    <select id="event" name="event" class="form-control">
                        <option value="">All events</option>
                        @foreach ($events as $ev)
                            <option value="{{ $ev }}" @selected(request('event') === $ev)>
                                {{ ucfirst($ev) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="model">Model</label>
                    <select id="model" name="model" class="form-control">
                        <option value="">All models</option>
                        @foreach ($models as $m)
                            <option value="{{ $m['value'] }}" @selected(request('model') === $m['value'])>
                                {{ $m['label'] }}
                            </option>
                        @endforeach
                    </select>
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
                    <a href="{{ route('admin.audit.index') }}" class="btn btn-outline">Reset</a>
                </div>
            </form>
        </div>

        {{-- Table --}}
        <x-gentelella::table>
            <thead>
                <tr>
                    <th>When</th>
                    <th>Who</th>
                    <th>Event</th>
                    <th>Model</th>
                    <th>Record</th>
                    <th>IP</th>
                    <th style="text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($audits as $audit)
                    <tr>
                        <td style="white-space: nowrap;">
                            {{ $audit->created_at->format('d M Y, H:i') }}
                        </td>
                        <td>{{ $audit->user?->name ?? 'System' }}</td>
                        <td>
                            @php
                                $tone = match ($audit->event) {
                                    'created'  => 'green',
                                    'updated'  => 'blue',
                                    'deleted'  => 'red',
                                    'restored' => 'yellow',
                                    default    => null,
                                };
                            @endphp
                            <x-gentelella::badge :tone="$tone">{{ ucfirst($audit->event) }}</x-gentelella::badge>
                        </td>
                        <td>{{ class_basename($audit->auditable_type) }}</td>
                        <td class="cell-mono">#{{ $audit->auditable_id }}</td>
                        <td class="cell-mono">{{ $audit->ip_address ?? '—' }}</td>
                        <td style="text-align: right; white-space: nowrap;">
                            <a href="{{ route('admin.audit.show', $audit) }}"
                               class="btn btn-sm btn-outline">
                                View
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 32px 16px; color: var(--text-muted);">
                            No audit entries found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </x-gentelella::table>

        @if ($audits->hasPages())
            <div style="padding: 12px 16px; border-top: 1px solid var(--border-color-light);">
                {{ $audits->links() }}
            </div>
        @endif
    </x-gentelella::card>
@endsection