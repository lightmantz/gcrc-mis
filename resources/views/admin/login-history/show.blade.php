@extends('gentelella::page')

@section('title', 'Login Record')
@section('page_key', 'logins')

@section('content')
    <x-gentelella::page-header title="Login Record" pretitle="System">
        <x-slot:actions>
            <a href="{{ route('admin.login-history.index') }}" class="btn btn-outline">Back to log</a>
        </x-slot:actions>
    </x-gentelella::page-header>

    <x-gentelella::card title="Session Details">
        <dl style="display: grid; grid-template-columns: auto 1fr; gap: 8px 16px; font-size: 13px;">
            <dt style="color: var(--text-muted);">Email</dt>
            <dd>{{ $loginHistory->email }}</dd>

            <dt style="color: var(--text-muted);">User</dt>
            <dd>
                @if ($loginHistory->user)
                    <a href="{{ route('admin.users.show', $loginHistory->user) }}">
                        {{ $loginHistory->user->name }}
                    </a>
                @else
                    <span style="color: var(--text-muted);">Not matched</span>
                @endif
            </dd>

            <dt style="color: var(--text-muted);">Outcome</dt>
            <dd>
                @if ($loginHistory->successful)
                    <span class="status status-green">Successful</span>
                @else
                    <span class="status status-red">Failed</span>
                    — {{ $loginHistory->failure_reason ?? 'Unknown' }}
                @endif
            </dd>

            <dt style="color: var(--text-muted);">Logged in at</dt>
            <dd>{{ $loginHistory->logged_in_at?->format('d M Y, H:i:s') ?? '—' }}</dd>

            <dt style="color: var(--text-muted);">Logged out at</dt>
            <dd>
                {{ $loginHistory->logged_out_at?->format('d M Y, H:i:s') ?? 'Still open' }}
            </dd>

            @if ($loginHistory->logged_out_at && $loginHistory->logged_in_at)
                <dt style="color: var(--text-muted);">Session duration</dt>
                <dd>{{ $loginHistory->logged_in_at->diffForHumans($loginHistory->logged_out_at, true) }}</dd>
            @endif

            <dt style="color: var(--text-muted);">IP Address</dt>
            <dd class="cell-mono">{{ $loginHistory->ip_address }}</dd>

            <dt style="color: var(--text-muted);">User Agent</dt>
            <dd style="word-break: break-all; font-size: 12px;">
                {{ $loginHistory->user_agent ?? '—' }}
            </dd>
        </dl>
    </x-gentelella::card>
@endsection