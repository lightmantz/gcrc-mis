@extends('gentelella::page')

@section('title', $plan->discipline_label . ' — ' . $plan->child->full_name)
@section('page_key', 'treatment-plans')

@section('content')
    <x-gentelella::page-header title="{{ $plan->discipline_label }} Plan"
                                pretitle="{{ $plan->child->full_name }} — {{ $plan->child->child_number }}">
        <x-slot:actions>
            @can('update', $plan)
                <a href="{{ route('admin.treatment-plans.edit', $plan) }}" class="btn btn-outline">Edit Plan</a>
            @endcan

            @can('activate', $plan)
                <form method="POST" action="{{ route('admin.treatment-plans.activate', $plan) }}"
                      style="display: inline;"
                      onsubmit="return confirm('Activate this plan? After activation, goals can still be updated but the plan content becomes stable.');">
                    @csrf
                    <button type="submit" class="btn btn-primary">Activate Plan</button>
                </form>
            @endcan

            @can('close', $plan)
                <button type="button" class="btn btn-danger"
                        onclick="document.getElementById('close-form').style.display = 'block'; this.style.display = 'none';">
                    Close Plan
                </button>
            @endcan
        </x-slot:actions>
    </x-gentelella::page-header>

    @if (session('error'))
        <div class="alert alert-error" style="margin-bottom: 16px;">
            <div class="alert-body">{{ session('error') }}</div>
        </div>
    @endif

    {{-- ─── Close form (hidden until requested) ─────── --}}
    @can('close', $plan)
        <x-gentelella::card id="close-form" style="display: none; margin-bottom: 16px;">
            <form method="POST" action="{{ route('admin.treatment-plans.close', $plan) }}">
                @csrf

                <div class="form-row cols-3">
                    <div class="form-group">
                        <label class="form-label">Outcome <span class="required">*</span></label>
                        <select name="status" class="form-control" required>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">End date <span class="required">*</span></label>
                        <input type="date" name="end_date" class="form-control"
                               value="{{ now()->toDateString() }}"
                               min="{{ $plan->start_date->toDateString() }}"
                               max="{{ now()->toDateString() }}" required>
                    </div>
                    <div class="form-group" style="display: flex; align-items: end;">
                        <button type="submit" class="btn btn-primary">Confirm</button>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Reason <span class="required">*</span></label>
                    <textarea name="closure_reason" class="form-control" rows="2" required></textarea>
                </div>
            </form>
        </x-gentelella::card>
    @endcan

    <div class="row col-8-4">
        <div>
            {{-- ─── Summary ─────────────────────────────── --}}
            <x-gentelella::card title="Plan Summary">
                <dl style="display: grid; grid-template-columns: auto 1fr; gap: 8px 16px; font-size: 13px;">
                    <dt style="color: var(--text-muted);">Child</dt>
                    <dd>
                        <a href="{{ route('admin.children.show', $plan->child) }}">
                            {{ $plan->child->full_name }}
                        </a>
                    </dd>

                    <dt style="color: var(--text-muted);">Discipline</dt>
                    <dd>{{ $plan->discipline_label }}</dd>

                    <dt style="color: var(--text-muted);">Lead clinician</dt>
                    <dd>{{ $plan->leadStaff->full_name ?? '—' }}</dd>

                    <dt style="color: var(--text-muted);">Status</dt>
                    <dd>
                        <x-gentelella::badge :tone="$plan->status_tone">{{ $plan->status_label }}</x-gentelella::badge>
                        @if ($plan->activated_at)
                            <span style="color: var(--text-muted); font-size: 12px;">
                                activated {{ $plan->activated_at->diffForHumans() }}
                                @if ($plan->activatedBy) by {{ $plan->activatedBy->name }} @endif
                            </span>
                        @endif
                        @if ($plan->closed_at)
                            <span style="color: var(--text-muted); font-size: 12px;">
                                closed {{ $plan->closed_at->diffForHumans() }}
                                @if ($plan->closedBy) by {{ $plan->closedBy->name }} @endif
                            </span>
                        @endif
                    </dd>

                    <dt style="color: var(--text-muted);">Start date</dt>
                    <dd>{{ $plan->start_date->format('d M Y') }}</dd>

                    @if ($plan->target_review_date)
                        <dt style="color: var(--text-muted);">Target review</dt>
                        <dd>{{ $plan->target_review_date->format('d M Y') }}</dd>
                    @endif

                    @if ($plan->next_review_date)
                        <dt style="color: var(--text-muted);">Next review</dt>
                        <dd>
                            {{ $plan->next_review_date->format('d M Y') }}
                            @if ($plan->isOverdueForReview())
                                <x-gentelella::badge tone="red">Overdue</x-gentelella::badge>
                            @endif
                        </dd>
                    @endif

                    @if ($plan->end_date)
                        <dt style="color: var(--text-muted);">Ended</dt>
                        <dd>{{ $plan->end_date->format('d M Y') }}</dd>
                    @endif

                    <dt style="color: var(--text-muted);">Review cycle</dt>
                    <dd>{{ $plan->review_cycle_label }}</dd>
                </dl>
            </x-gentelella::card>

            {{-- ─── Diagnoses Addressed ─────────────────── --}}
            @if ($plan->diagnoses->isNotEmpty())
                <x-gentelella::card title="Diagnoses Addressed" style="margin-top: 16px;">
                    @foreach ($plan->diagnoses as $diagnosis)
                        <div style="padding: 8px 0; border-bottom: 1px solid var(--border-color-light);">
                            <a href="{{ route('admin.diagnoses.show', $diagnosis) }}" style="font-size: 13px; font-weight: 500;">
                                {{ $diagnosis->display_label }}
                            </a>
                            <div style="display: flex; gap: 4px; margin-top: 4px;">
                                <x-gentelella::badge :tone="$diagnosis->diagnosis_type_tone">
                                    {{ $diagnosis->diagnosis_type_label }}
                                </x-gentelella::badge>
                                <x-gentelella::badge :tone="$diagnosis->severity_tone">
                                    {{ $diagnosis->severity_label }}
                                </x-gentelella::badge>
                                <x-gentelella::badge :tone="$diagnosis->status_tone">
                                    {{ $diagnosis->status_label }}
                                </x-gentelella::badge>
                            </div>
                        </div>
                    @endforeach
                </x-gentelella::card>
            @endif

            {{-- ─── Objectives & Notes ──────────────────── --}}
            @if ($plan->overall_objectives)
                <x-gentelella::card title="Overall Objectives" style="margin-top: 16px;">
                    <p style="white-space: pre-line; font-size: 13px;">{{ $plan->overall_objectives }}</p>
                </x-gentelella::card>
            @endif

            @if ($plan->notes)
                <x-gentelella::card title="Notes" style="margin-top: 16px;">
                    <p style="white-space: pre-line; font-size: 13px;">{{ $plan->notes }}</p>
                </x-gentelella::card>
            @endif

            {{-- ─── Goals ──────────────────────────────── --}}
            @include('admin.treatment-plans.partials._goals', ['plan' => $plan])
        </div>

        <div>
            {{-- ─── Progress Ring ──────────────────────── --}}
            @php $progress = $plan->overall_progress; @endphp
            @if ($progress !== null)
                <x-gentelella::card title="Overall Progress">
                    <div style="text-align: center; padding: 8px 0;">
                        <div style="font-size: 36px; font-weight: 600; color: var(--primary);">{{ $progress }}%</div>
                        <div style="color: var(--text-muted); font-size: 12px; margin-top: 4px;">
                            {{ $plan->achieved_goals }} of {{ $plan->total_goals }} goals achieved
                        </div>
                    </div>
                </x-gentelella::card>
            @endif

            {{-- ─── Team Members ──────────────────────── --}}
            <x-gentelella::card title="Team" style="margin-top: 16px;">
                <div style="padding: 8px 0; border-bottom: 1px solid var(--border-color-light);">
                    <div style="display: flex; justify-content: space-between; align-items: baseline;">
                        <a href="{{ route('admin.staff.show', $plan->leadStaff) }}" style="font-size: 13px; font-weight: 500;">
                            {{ $plan->leadStaff->full_name }}
                        </a>
                        <x-gentelella::badge tone="teal">Lead</x-gentelella::badge>
                    </div>
                    <div style="font-size: 11.5px; color: var(--text-muted);">
                        {{ $plan->leadStaff->category_label }}
                    </div>
                </div>

                @foreach ($plan->teamMembers as $member)
                    <div style="padding: 8px 0; border-bottom: 1px solid var(--border-color-light);">
                        <a href="{{ route('admin.staff.show', $member) }}" style="font-size: 13px;">
                            {{ $member->full_name }}
                        </a>
                        @if ($member->pivot->role)
                            <div style="font-size: 11.5px; color: var(--text-muted);">
                                {{ $member->pivot->role }}
                            </div>
                        @endif
                    </div>
                @endforeach
            </x-gentelella::card>

            {{-- ─── Danger Zone ──────────────────────── --}}
            @can('delete', $plan)
                <x-gentelella::card title="Danger Zone" style="margin-top: 16px;">
                    <form method="POST" action="{{ route('admin.treatment-plans.destroy', $plan) }}"
                          onsubmit="return confirm('Delete this draft plan? It can be restored later.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete Draft Plan</button>
                    </form>
                </x-gentelella::card>
            @endcan
        </div>
    </div>
@endsection