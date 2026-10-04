@extends('gentelella::page')

@section('title', 'Dashboard')
@section('page_key', 'dashboard')

@section('content')
    <x-gentelella::page-header title="Welcome, {{ $user->name }}"
                                pretitle="{{ $roles->first() ?? 'User' }}" />

    {{-- ═══ Primary stats ═══ --}}
    <div class="row col-4">
        @if ($stats['children']['total'] !== null)
            <x-gentelella::card>
                <div style="padding: 4px 0;">
                    <div style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.4px; color: var(--text-muted);">
                        Children
                    </div>
                    <div style="font-size: 32px; font-weight: 600; line-height: 1.1; margin: 6px 0;">
                        {{ number_format($stats['children']['total']) }}
                    </div>
                    <div style="font-size: 12px; color: var(--text-muted);">
                        <strong style="color: var(--green);">{{ $stats['children']['active'] }}</strong> active
                        · {{ $stats['children']['new_30'] }} new in 30 days
                    </div>
                </div>
            </x-gentelella::card>
        @endif

        @if ($stats['assessments']['total'] !== null)
            <x-gentelella::card>
                <div style="padding: 4px 0;">
                    <div style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.4px; color: var(--text-muted);">
                        Assessments
                    </div>
                    <div style="font-size: 32px; font-weight: 600; line-height: 1.1; margin: 6px 0;">
                        {{ number_format($stats['assessments']['total']) }}
                    </div>
                    <div style="font-size: 12px; color: var(--text-muted);">
                        <strong style="color: var(--yellow);">{{ $stats['assessments']['drafts'] }}</strong> drafts
                        · {{ $stats['assessments']['finalized'] }} finalized
                    </div>
                </div>
            </x-gentelella::card>
        @endif

        @if ($stats['staff']['total'] !== null)
            <x-gentelella::card>
                <div style="padding: 4px 0;">
                    <div style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.4px; color: var(--text-muted);">
                        Staff
                    </div>
                    <div style="font-size: 32px; font-weight: 600; line-height: 1.1; margin: 6px 0;">
                        {{ number_format($stats['staff']['total']) }}
                    </div>
                    <div style="font-size: 12px; color: var(--text-muted);">
                        <strong style="color: var(--green);">{{ $stats['staff']['active'] }}</strong> active
                    </div>
                </div>
            </x-gentelella::card>
        @endif

        @if ($stats['referrals']['total'] !== null)
            <x-gentelella::card>
                <div style="padding: 4px 0;">
                    <div style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.4px; color: var(--text-muted);">
                        Referrals
                    </div>
                    <div style="font-size: 32px; font-weight: 600; line-height: 1.1; margin: 6px 0;">
                        {{ number_format($stats['referrals']['total']) }}
                    </div>
                    <div style="font-size: 12px; color: var(--text-muted);">
                        <strong style="color: var(--blue);">{{ $stats['referrals']['received'] }}</strong> awaiting action
                    </div>
                </div>
            </x-gentelella::card>
        @endif
    </div>

    {{-- ═══ Secondary row ═══ --}}
    <div class="row col-8-4" style="margin-top: 16px;">
        <div>
            {{-- Recent assessments --}}
            @if ($recentAssessments->isNotEmpty())
                <x-gentelella::card title="Recent Assessments">
                    <x-gentelella::table>
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Child</th>
                                <th>Type</th>
                                <th>Assessor</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($recentAssessments as $assessment)
                                <tr>
                                    <td>{{ $assessment->assessment_date->format('d M') }}</td>
                                    <td>
                                        <a href="{{ route('admin.children.show', $assessment->child) }}">
                                            {{ $assessment->child->full_name }}
                                        </a>
                                    </td>
                                    <td>{{ $assessment->type_label }}</td>
                                    <td>{{ $assessment->assessor->full_name ?? '—' }}</td>
                                    <td>
                                        <x-gentelella::badge :tone="$assessment->status_tone">
                                            {{ $assessment->status_label }}
                                        </x-gentelella::badge>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </x-gentelella::table>
                </x-gentelella::card>
            @endif

            {{-- Pending drafts --}}
            @if ($pendingDrafts->isNotEmpty())
                <x-gentelella::card title="Drafts Awaiting Finalization" style="margin-top: 16px;">
                    @foreach ($pendingDrafts as $draft)
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 8px 0; border-bottom: 1px solid var(--border-color-light);">
                            <div>
                                <a href="{{ route('admin.assessments.show', $draft) }}" style="font-size: 13px; font-weight: 500;">
                                    {{ $draft->type_label }}
                                </a>
                                <div style="font-size: 11.5px; color: var(--text-muted);">
                                    {{ $draft->child->full_name }}
                                    — {{ $draft->assessment_date->format('d M Y') }}
                                    — {{ $draft->assessor->full_name ?? '—' }}
                                </div>
                            </div>
                            <a href="{{ route('admin.assessments.edit', $draft) }}"
                               class="btn btn-sm btn-outline">Open</a>
                        </div>
                    @endforeach
                </x-gentelella::card>
            @endif

            {{-- Recent children --}}
            @if ($recentChildren->isNotEmpty())
                <x-gentelella::card title="Recently Registered Children" style="margin-top: 16px;">
                    <x-gentelella::table>
                        <thead>
                            <tr>
                                <th>Child #</th>
                                <th>Name</th>
                                <th>Age</th>
                                <th>Registered</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($recentChildren as $child)
                                <tr>
                                    <td class="cell-mono">{{ $child->child_number }}</td>
                                    <td>
                                        <a href="{{ route('admin.children.show', $child) }}">
                                            {{ $child->full_name }}
                                        </a>
                                    </td>
                                    <td>{{ $child->age_display }}</td>
                                    <td>{{ $child->registration_date->format('d M Y') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </x-gentelella::table>
                </x-gentelella::card>
            @endif
        </div>

        <div>
            {{-- Children by condition --}}
            @if ($childrenByCondition->isNotEmpty())
                <x-gentelella::card title="Children by Primary Condition">
                    @php
                        $total = $childrenByCondition->sum();
                    @endphp
                    @foreach ($childrenByCondition as $condition => $count)
                        @php
                            $label = \App\Models\Child::where('primary_condition', $condition)->first()?->primary_condition_label
                                ?? ucfirst(str_replace('_', ' ', $condition));
                            $pct = $total > 0 ? round(($count / $total) * 100) : 0;
                        @endphp
                        <div style="margin-bottom: 10px;">
                            <div style="display: flex; justify-content: space-between; font-size: 12.5px; margin-bottom: 3px;">
                                <span>{{ $label }}</span>
                                <span style="color: var(--text-muted);">{{ $count }} · {{ $pct }}%</span>
                            </div>
                            <div style="height: 4px; background: var(--border-color-light); border-radius: 2px; overflow: hidden;">
                                <div style="height: 100%; width: {{ $pct }}%; background: var(--primary);"></div>
                            </div>
                        </div>
                    @endforeach
                </x-gentelella::card>
            @endif

            {{-- Pending referrals --}}
            @if ($pendingReferrals->isNotEmpty())
                <x-gentelella::card title="Referrals Awaiting Action" style="margin-top: 16px;">
                    @foreach ($pendingReferrals as $referral)
                        <div style="padding: 8px 0; border-bottom: 1px solid var(--border-color-light);">
                            <a href="{{ route('admin.children.show', $referral->child) }}"
                               style="font-size: 13px; font-weight: 500;">
                                {{ $referral->child->full_name ?? '—' }}
                            </a>
                            <div style="font-size: 11.5px; color: var(--text-muted); margin-top: 2px;">
                                {{ $referral->source_type_label }}
                                @if ($referral->source_name)
                                    — {{ $referral->source_name }}
                                @endif
                                — {{ $referral->referral_date->format('d M Y') }}
                            </div>
                            <div style="margin-top: 4px;">
                                <x-gentelella::badge :tone="$referral->status_tone">
                                    {{ $referral->status_label }}
                                </x-gentelella::badge>
                            </div>
                        </div>
                    @endforeach
                </x-gentelella::card>
            @endif

            {{-- Staff snapshot --}}
            @if ($stats['staff']['total'] !== null)
                <x-gentelella::card title="Staff Snapshot" style="margin-top: 16px;">
                    @php
                        $byCategory = \App\Models\Staff::selectRaw('category, count(*) as total')
                            ->groupBy('category')
                            ->pluck('total', 'category');
                    @endphp
                    @foreach ($byCategory as $category => $count)
                        <div style="display: flex; justify-content: space-between; padding: 6px 0; font-size: 12.5px; border-bottom: 1px solid var(--border-color-light);">
                            <span>{{ ucfirst(str_replace('_', ' ', $category)) }}</span>
                            <strong>{{ $count }}</strong>
                        </div>
                    @endforeach
                </x-gentelella::card>
            @endif
        </div>
    </div>

    {{-- ═══ Your account ═══ --}}
    <x-gentelella::card title="Your Account" style="margin-top: 16px;">
        <dl style="display: grid; grid-template-columns: auto 1fr; gap: 8px 16px; font-size: 13px;">
            <dt style="color: var(--text-muted);">Name</dt>
            <dd>{{ $user->name }}</dd>

            <dt style="color: var(--text-muted);">Email</dt>
            <dd>{{ $user->email }}</dd>

            <dt style="color: var(--text-muted);">Roles</dt>
            <dd>
                @foreach ($roles as $role)
                    <x-gentelella::badge tone="teal">{{ $role }}</x-gentelella::badge>
                @endforeach
            </dd>

            <dt style="color: var(--text-muted);">Last login</dt>
            <dd>{{ $user->last_login_at?->diffForHumans() ?? 'Never' }}</dd>
        </dl>
    </x-gentelella::card>
@endsection