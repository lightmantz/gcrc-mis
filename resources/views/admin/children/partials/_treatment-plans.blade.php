@php
    $activePlans = $child->treatmentPlans()
        ->with(['leadStaff:id,first_name,middle_name,last_name'])
        ->withCount('goals')
        ->active()
        ->get();

    $pastPlans = $child->treatmentPlans()
        ->whereIn('status', ['completed', 'cancelled'])
        ->with('leadStaff:id,first_name,middle_name,last_name')
        ->withCount('goals')
        ->limit(5)
        ->get();

    $draftPlans = $child->treatmentPlans()
        ->where('status', 'draft')
        ->with('leadStaff:id,first_name,middle_name,last_name')
        ->withCount('goals')
        ->get();
@endphp

<x-gentelella::card title="Treatment Plans" style="margin-top: 16px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; gap: 12px; flex-wrap: wrap;">
        <span style="color: var(--text-muted); font-size: 12px;">
            {{ $activePlans->count() }} active
            @if ($draftPlans->count() > 0)
                · {{ $draftPlans->count() }} draft
            @endif
        </span>

        @can('treatment_plans.create')
            <a href="{{ route('admin.treatment-plans.create', ['child_id' => $child->id]) }}"
               class="btn btn-sm btn-primary">
                New Plan
            </a>
        @endcan
    </div>

    @if ($draftPlans->isNotEmpty())
        <div style="margin-bottom: 12px;">
            <div style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.4px; color: var(--text-muted); margin-bottom: 6px;">
                Drafts
            </div>
            @foreach ($draftPlans as $plan)
                <div style="border: 1px dashed var(--border-color); border-radius: var(--radius); padding: 8px 12px; margin-bottom: 6px; display: flex; justify-content: space-between; align-items: center; gap: 12px;">
                    <div>
                        <a href="{{ route('admin.treatment-plans.show', $plan) }}" style="font-size: 13px; font-weight: 500;">
                            {{ $plan->discipline_label }}
                        </a>
                        <div style="font-size: 11.5px; color: var(--text-muted);">
                            Lead: {{ $plan->leadStaff->full_name ?? '—' }}
                            · {{ $plan->goals_count }} {{ Str::plural('goal', $plan->goals_count) }}
                        </div>
                    </div>
                    <x-gentelella::badge tone="yellow">Draft</x-gentelella::badge>
                </div>
            @endforeach
        </div>
    @endif

    @if ($activePlans->isNotEmpty())
        <div style="margin-bottom: 12px;">
            <div style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.4px; color: var(--text-muted); margin-bottom: 6px;">
                Active
            </div>
            @foreach ($activePlans as $plan)
                <div style="border: 1px solid var(--border-color); border-radius: var(--radius); padding: 10px 12px; margin-bottom: 8px;">
                    <div style="display: flex; justify-content: space-between; align-items: start; gap: 12px;">
                        <div style="min-width: 0; flex: 1;">
                            <a href="{{ route('admin.treatment-plans.show', $plan) }}" style="font-size: 13.5px; font-weight: 500;">
                                {{ $plan->discipline_label }}
                            </a>
                            <div style="font-size: 11.5px; color: var(--text-muted); margin-top: 2px;">
                                Lead: {{ $plan->leadStaff->full_name ?? '—' }}
                                · Started {{ $plan->start_date->format('d M Y') }}
                            </div>
                            <div style="display: flex; gap: 4px; margin-top: 6px; flex-wrap: wrap;">
                                <x-gentelella::badge :tone="$plan->status_tone">{{ $plan->status_label }}</x-gentelella::badge>
                                @if ($plan->next_review_date)
                                    <x-gentelella::badge tone="blue">Review {{ $plan->next_review_date->format('d M') }}</x-gentelella::badge>
                                @endif
                                @if ($plan->isOverdueForReview())
                                    <x-gentelella::badge tone="red">Overdue</x-gentelella::badge>
                                @endif
                            </div>
                            @php $progress = $plan->overall_progress; @endphp
                            @if ($progress !== null)
                                <div style="margin-top: 8px;">
                                    <div style="display: flex; justify-content: space-between; font-size: 11px; color: var(--text-muted); margin-bottom: 3px;">
                                        <span>{{ $plan->achieved_goals }} / {{ $plan->total_goals }} goals</span>
                                        <span>{{ $progress }}%</span>
                                    </div>
                                    <div style="height: 4px; background: var(--border-color-light); border-radius: 2px; overflow: hidden;">
                                        <div style="height: 100%; width: {{ $progress }}%; background: var(--primary);"></div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    @if ($pastPlans->isNotEmpty())
        <details>
            <summary style="cursor: pointer; font-size: 12px; color: var(--text-muted); padding-top: 8px; border-top: 1px solid var(--border-color-light);">
                Past plans ({{ $pastPlans->count() }})
            </summary>
            <div style="margin-top: 8px;">
                @foreach ($pastPlans as $plan)
                    <div style="display: flex; justify-content: space-between; gap: 12px; font-size: 12.5px; padding: 6px 0;">
                        <div>
                            <a href="{{ route('admin.treatment-plans.show', $plan) }}">
                                {{ $plan->discipline_label }}
                            </a>
                            <div style="font-size: 11px; color: var(--text-muted);">
                                {{ $plan->start_date->format('d M Y') }}
                                @if ($plan->end_date)
                                    → {{ $plan->end_date->format('d M Y') }}
                                @endif
                            </div>
                        </div>
                        <x-gentelella::badge :tone="$plan->status_tone">{{ $plan->status_label }}</x-gentelella::badge>
                    </div>
                @endforeach
            </div>
        </details>
    @endif

    @if ($activePlans->isEmpty() && $draftPlans->isEmpty() && $pastPlans->isEmpty())
        <p style="color: var(--text-muted); font-size: 13px;">
            No treatment plans created yet.
        </p>
    @endif
</x-gentelella::card>