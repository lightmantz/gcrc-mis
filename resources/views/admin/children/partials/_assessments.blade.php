@php
    $assessments = $child->assessments()
        ->with('assessor:id,first_name,middle_name,last_name')
        ->latest('assessment_date')
        ->limit(10)
        ->get();
    $totalAssessments = $child->assessments()->count();

    // Count per type, for the summary chips
    $byType = $child->assessments()
        ->selectRaw('type, count(*) as total')
        ->groupBy('type')
        ->pluck('total', 'type');
@endphp

<x-gentelella::card title="Assessments" style="margin-top: 16px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; gap: 12px; flex-wrap: wrap;">
        <span style="color: var(--text-muted); font-size: 12px;">
            {{ $totalAssessments }} {{ Str::plural('assessment', $totalAssessments) }} recorded
        </span>

        @can('assessments.create')
            <a href="{{ route('admin.assessments.create', ['child_id' => $child->id]) }}"
               class="btn btn-sm btn-primary">
                New Assessment
            </a>
        @endcan
    </div>

    @if ($byType->isNotEmpty())
        <div style="display: flex; flex-wrap: wrap; gap: 4px; margin-bottom: 12px; padding-bottom: 12px; border-bottom: 1px solid var(--border-color-light);">
            @foreach ($byType as $typeKey => $count)
                @php
                    $typeInstance = \App\Assessments\AssessmentTypeRegistry::get($typeKey);
                    $label = $typeInstance?->label() ?? ucfirst($typeKey);
                @endphp
                <a href="{{ route('admin.assessments.index', ['child' => $child->id, 'type' => $typeKey]) }}"
                   style="text-decoration: none;">
                    <x-gentelella::badge tone="teal">
                        {{ $label }} · {{ $count }}
                    </x-gentelella::badge>
                </a>
            @endforeach
        </div>
    @endif

    @if ($assessments->isEmpty())
        <p style="color: var(--text-muted); font-size: 13px;">No assessments recorded yet.</p>
    @else
        <div style="display: flex; flex-direction: column; gap: 8px;">
            @foreach ($assessments as $assessment)
                <div style="border: 1px solid var(--border-color); border-radius: var(--radius); padding: 10px 12px; display: flex; justify-content: space-between; align-items: center; gap: 12px;">
                    <div>
                        <a href="{{ route('admin.assessments.show', $assessment) }}" style="font-size: 13.5px; font-weight: 500;">
                            {{ $assessment->type_label }}
                        </a>
                        <div style="font-size: 11.5px; color: var(--text-muted); margin-top: 2px;">
                            {{ $assessment->assessment_date->format('d M Y') }}
                            @if ($assessment->assessor)
                                — {{ $assessment->assessor->full_name }}
                            @endif
                        </div>
                    </div>
                    <x-gentelella::badge :tone="$assessment->status_tone">
                        {{ $assessment->status_label }}
                    </x-gentelella::badge>
                </div>
            @endforeach
        </div>

        @if ($totalAssessments > 10)
            <div style="margin-top: 10px; text-align: right;">
                <a href="{{ route('admin.assessments.index', ['child' => $child->id]) }}"
                   style="font-size: 12.5px;">
                    View all {{ $totalAssessments }} assessments →
                </a>
            </div>
        @endif
    @endif
</x-gentelella::card>