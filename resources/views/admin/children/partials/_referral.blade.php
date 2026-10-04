@php
    $referrals = $child->referrals()->with('registeredBy')->get();
    $latest = $referrals->first();
@endphp

<x-gentelella::card title="Referral Information" style="margin-top: 16px;">
    @if ($latest)
        <dl style="display: grid; grid-template-columns: auto 1fr; gap: 8px 16px; font-size: 13px; margin-bottom: 16px;">
            <dt style="color: var(--text-muted);">Source</dt>
            <dd>
                {{ $latest->source_type_label }}
                @if ($latest->source_name)
                    — {{ $latest->source_name }}
                @endif
            </dd>

            @if ($latest->source_contact)
                <dt style="color: var(--text-muted);">Contact</dt>
                <dd>{{ $latest->source_contact }}</dd>
            @endif

            <dt style="color: var(--text-muted);">Date</dt>
            <dd>{{ $latest->referral_date->format('d M Y') }}</dd>

            <dt style="color: var(--text-muted);">Status</dt>
            <dd>
                <x-gentelella::badge :tone="$latest->status_tone">
                    {{ $latest->status_label }}
                </x-gentelella::badge>
            </dd>

            @if ($latest->reason)
                <dt style="color: var(--text-muted);">Reason</dt>
                <dd style="white-space: pre-line;">{{ $latest->reason }}</dd>
            @endif

            <dt style="color: var(--text-muted);">Recorded by</dt>
            <dd>{{ $latest->registeredBy?->name ?? 'System' }}</dd>
        </dl>

        @if ($referrals->count() > 1)
            <details>
                <summary style="cursor: pointer; font-size: 12.5px; color: var(--primary); margin-bottom: 8px;">
                    Earlier referrals ({{ $referrals->count() - 1 }})
                </summary>
                <ul style="margin-top: 8px; padding-left: 20px; font-size: 12.5px; color: var(--text-muted);">
                    @foreach ($referrals->skip(1) as $ref)
                        <li>
                            {{ $ref->referral_date->format('d M Y') }} —
                            {{ $ref->source_type_label }}
                            @if ($ref->source_name) ({{ $ref->source_name }}) @endif
                            — <x-gentelella::badge :tone="$ref->status_tone">{{ $ref->status_label }}</x-gentelella::badge>
                        </li>
                    @endforeach
                </ul>
            </details>
        @endif
    @else
        <p style="color: var(--text-muted); font-size: 13px; margin-bottom: 16px;">
            No referral information recorded.
        </p>
    @endif

    @can('children.edit')
        <details>
            <summary class="btn btn-outline" style="cursor: pointer; list-style: none;">
                Record Referral
            </summary>

            <div style="margin-top: 12px; padding: 16px; border: 1px solid var(--border-color); border-radius: var(--radius);">
                <form method="POST" action="{{ route