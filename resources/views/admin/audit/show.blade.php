@extends('gentelella::page')

@section('title', 'Audit Entry')
@section('page_key', 'audit')

@section('content')
    <x-gentelella::page-header title="Audit Entry" pretitle="System">
        <x-slot:actions>
            <a href="{{ route('admin.audit.index') }}" class="btn btn-outline">Back to log</a>
        </x-slot:actions>
    </x-gentelella::page-header>

    <div class="row col-4-8">
        <div>
            <x-gentelella::card title="Summary">
                <dl style="display: grid; grid-template-columns: auto 1fr; gap: 8px 16px; font-size: 13px;">
                    <dt style="color: var(--text-muted);">When</dt>
                    <dd>{{ $audit->created_at->format('d M Y, H:i:s') }}</dd>

                    <dt style="color: var(--text-muted);">Who</dt>
                    <dd>{{ $audit->user?->name ?? 'System' }}</dd>

                    <dt style="color: var(--text-muted);">Event</dt>
                    <dd>{{ ucfirst($audit->event) }}</dd>

                    <dt style="color: var(--text-muted);">Model</dt>
                    <dd>{{ $model['label'] }}</dd>

                    <dt style="color: var(--text-muted);">Record</dt>
                    <dd class="cell-mono">
                        #{{ $audit->auditable_id }}
                        @if ($recordUrl)
                            — <a href="{{ $recordUrl }}">open record</a>
                        @endif
                    </dd>

                    <dt style="color: var(--text-muted);">IP</dt>
                    <dd class="cell-mono">{{ $audit->ip_address ?? '—' }}</dd>

                    <dt style="color: var(--text-muted);">URL</dt>
                    <dd style="word-break: break-all;">
                        <span class="cell-mono" style="font-size: 11.5px;">
                            {{ $audit->url ?? '—' }}
                        </span>
                    </dd>
                </dl>
            </x-gentelella::card>
        </div>

        <div>
            @if ($oldPermissions || $newPermissions)
                <x-gentelella::card title="Permission Changes">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
                        <div>
                            <div style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.4px; color: var(--text-muted); margin-bottom: 8px;">
                                Before
                            </div>
                            @forelse ($oldPermissions as $module => $group)
                                <div style="margin-bottom: 10px;">
                                    <strong style="font-size: 12px;">{{ $group['label'] }}</strong>
                                    <div style="display: flex; flex-wrap: wrap; gap: 4px; margin-top: 4px;">
                                        @foreach ($group['actions'] as $action)
                                            <x-gentelella::badge tone="red">{{ $action }}</x-gentelella::badge>
                                        @endforeach
                                    </div>
                                </div>
                            @empty
                                <p style="color: var(--text-muted); font-size: 12px;">None</p>
                            @endforelse
                        </div>
                        <div>
                            <div style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.4px; color: var(--text-muted); margin-bottom: 8px;">
                                After
                            </div>
                            @forelse ($newPermissions as $module => $group)
                                <div style="margin-bottom: 10px;">
                                    <strong style="font-size: 12px;">{{ $group['label'] }}</strong>
                                    <div style="display: flex; flex-wrap: wrap; gap: 4px; margin-top: 4px;">
                                        @foreach ($group['actions'] as $action)
                                            <x-gentelella::badge tone="green">{{ $action }}</x-gentelella::badge>
                                        @endforeach
                                    </div>
                                </div>
                            @empty
                                <p style="color: var(--text-muted); font-size: 12px;">None</p>
                            @endforelse
                        </div>
                    </div>
                </x-gentelella::card>
            @endif

            <x-gentelella::card title="Field Changes">
                @if (empty($changedKeys))
                    <p style="color: var(--text-muted); font-size: 13px;">
                        No field-level changes recorded for this event.
                    </p>
                @else
                    <x-gentelella::table>
                        <thead>
                            <tr>
                                <th style="width: 160px;">Field</th>
                                <th>Before</th>
                                <th>After</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($changedKeys as $key)
                                <tr>
                                    <td class="cell-strong">{{ $key }}</td>
                                    <td class="cell-mono" style="font-size: 11.5px; word-break: break-all;">
                                        {{ $this->renderValue($old[$key] ?? null) }}
                                    </td>
                                    <td class="cell-mono" style="font-size: 11.5px; word-break: break-all;">
                                        {{ $this->renderValue($new[$key] ?? null) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </x-gentelella::table>
                @endif
            </x-gentelella::card>
        </div>
    </div>
@endsection