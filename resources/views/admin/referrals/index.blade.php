@extends('gentelella::page')

@section('title', 'Referrals')
@section('page_key', 'referrals')

@section('content')
    <x-gentelella::page-header title="Referrals" pretitle="Registry" />

    <x-gentelella::card flush>
        <div class="card-body" style="border-bottom: 1px solid var(--border-color-light);">
            <form method="GET" class="form-row" style="align-items: end;">
                <div class="form-group">
                    <label class="form-label" for="status">Status</label>
                    <select id="status" name="status" class="form-control">
                        <option value="">All</option>
                        @foreach (['received' => 'Received', 'accepted' => 'Accepted', 'in_progress' => 'In Progress', 'completed' => 'Completed', 'declined' => 'Declined'] as $v => $l)
                            <option value="{{ $v }}" @selected(request('status') === $v)>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="source">Source</label>
                    <select id="source" name="source" class="form-control">
                        <option value="">All sources</option>
                        @foreach (['walk_in' => 'Walk-in', 'hospital' => 'Hospital', 'clinic' => 'Clinic', 'school' => 'School', 'community' => 'Community', 'self' => 'Self', 'other' => 'Other'] as $v => $l)
                            <option value="{{ $v }}" @selected(request('source') === $v)>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-actions" style="margin: 0; padding: 0; border: none;">
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <a href="{{ route('admin.referrals.index') }}" class="btn btn-outline">Reset</a>
                </div>
            </form>
        </div>

        <x-gentelella::table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Child</th>
                    <th>Source</th>
                    <th>Status</th>
                    <th>Recorded by</th>
                    <th style="text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($referrals as $referral)
                    <tr>
                        <td>{{ $referral->referral_date->format('d M Y') }}</td>
                        <td>
                            @if ($referral->child)
                                <a href="{{ route('admin.children.show', $referral->child) }}">
                                    {{ $referral->child->full_name }}
                                </a>
                            @else
                                <span style="color: var(--text-muted);">— not linked —</span>
                            @endif
                        </td>
                        <td>
                            {{ $referral->source_type_label }}
                            @if ($referral->source_name)
                                <div style="font-size: 11.5px; color: var(--text-muted);">
                                    {{ $referral->source_name }}
                                </div>
                            @endif
                        </td>
                        <td>
                            <x-gentelella::badge :tone="$referral->status_tone">
                                {{ $referral->status_label }}
                            </x-gentelella::badge>
                        </td>
                        <td>{{ $referral->registeredBy?->name ?? 'System' }}</td>
                        <td style="text-align: right;">
                            @if ($referral->child)
                                <a href="{{ route('admin.children.show', $referral->child) }}"
                                   class="btn btn-sm btn-outline">Open child</a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 32px 16px; color: var(--text-muted);">
                            No referrals recorded yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </x-gentelella::table>

        @if ($referrals->hasPages())
            <div style="padding: 12px 16px; border-top: 1px solid var(--border-color-light);">
                {{ $referrals->links() }}
            </div>
        @endif
    </x-gentelella::card>
@endsection