@php
    $assessments = $child->assessments()
        ->with('assessor:id,first_name,middle_name,last_name')
        ->latest('assessment_date')
        ->limit(10)
        ->get();
    $totalAssessments = $child->assessments()->count();
@endphp

<x-gentelella::card title="Assessments" style="margin-top: 16px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
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