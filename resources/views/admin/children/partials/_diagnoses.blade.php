@php
    $activeDiagnoses = $child->diagnoses()->where('status', 'active')
        ->with('diagnosedBy:id,first_name,middle_name,last_name')
        ->orderByDesc('diagnosis_type')
        ->orderByDesc('diagnosed_at')
        ->get();

    $pastDiagnoses = $child->diagnoses()->where('status', '!=', 'active')
        ->with('diagnosedBy:id,first_name,middle_name,last_name')
        ->orderByDesc('diagnosed_at')
        ->limit(5)
        ->get();

    $totalDiagnoses = $child->diagnoses()->count();
@endphp

<x-gentelella::card title="Diagnoses & Conditions" style="margin-top: 16px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; gap: 12px; flex-wrap: wrap;">
        <span style="color: var(--text-muted); font-size: 12px;">
            {{ $activeDiagnoses->count() }} active
            @if ($totalDiagnoses > $activeDiagnoses->count())
                · {{ $totalDiagnoses - $activeDiagnoses->count() }} historical
            @endif
        </span>

        @can('diagnoses.create')
            <a href="{{ route('admin.diagnoses.create', ['child_id' => $child->id]) }}"
               class="btn btn-sm btn-primary">
                Record Diagnosis
            </a>
        @endcan
    </div>

    @if ($activeDiagnoses->isEmpty() && $pastDiagnoses->isEmpty())
        <p style="color: var(--text-muted); font-size: 13px;">
            No diagnoses recorded yet.
        </p>
    @else
        @if ($activeDiagnoses->isNotEmpty())
            <div style="margin-bottom: 12px;">
                <div style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.4px; color: var(--text-muted); margin-bottom: 8px;">
                    Active
                </div>
                <div style="display: flex; flex-direction: column; gap: 8px;">
                    @foreach ($activeDiagnoses as $diagnosis)
                        <div style="border: 1px solid var(--border-color); border-radius: var(--radius); padding: 10px 12px; display: flex; justify-content: space-between; align-items: start; gap: 12px;">
                            <div>
                                <a href="{{ route('admin.diagnoses.show', $diagnosis) }}" style="font-size: 13.5px; font-weight: 500;">
                                    {{ $diagnosis->display_label }}
                                </a>
                                <div style="font-size: 11.5px; color: var(--text-muted); margin-top: 2px;">
                                    {{ $diagnosis->category_label }}
                                    — diagnosed {{ $diagnosis->diagnosed_at->format('d M Y') }}
                                    @if ($diagnosis->diagnosedBy)
                                        by {{ $diagnosis->diagnosedBy->full_name }}
                                    @endif
                                </div>
                                <div style="display: flex; flex-wrap: wrap; gap: 4px; margin-top: 6px;">
                                    <x-gentelella::badge :tone="$diagnosis->diagnosis_type_tone">
                                        {{ $diagnosis->diagnosis_type_label }}
                                    </x-gentelella::badge>
                                    <x-gentelella::badge :tone="$diagnosis->severity_tone">
                                        {{ $diagnosis->severity_label }}
                                    </x-gentelella::badge>
                                    @if ($diagnosis->isProvisional())
                                        <x-gentelella::badge tone="yellow">Provisional</x-gentelella::badge>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        @if ($pastDiagnoses->isNotEmpty())
            <details>
                <summary style="cursor: pointer; font-size: 12px; color: var(--text-muted); padding-top: 8px; border-top: 1px solid var(--border-color-light);">
                    Past / resolved ({{ $pastDiagnoses->count() }})
                </summary>
                <div style="margin-top: 8px; display: flex; flex-direction: column; gap: 6px;">
                    @foreach ($pastDiagnoses as $diagnosis)
                        <div style="display: flex; justify-content: space-between; gap: 12px; font-size: 12.5px; padding: 6px 0;">
                            <div>
                                <a href="{{ route('admin.diagnoses.show', $diagnosis) }}">
                                    {{ $diagnosis->display_label }}
                                </a>
                                <div style="font-size: 11px; color: var(--text-muted);">
                                    {{ $diagnosis->diagnosed_at->format('d M Y') }}
                                    @if ($diagnosis->ended_at)
                                        → {{ $diagnosis->ended_at->format('d M Y') }}
                                    @endif
                                </div>
                            </div>
                            <x-gentelella::badge :tone="$diagnosis->status_tone">
                                {{ $diagnosis->status_label }}
                            </x-gentelella::badge>
                        </div>
                    @endforeach
                </div>
            </details>
        @endif
    @endif
</x-gentelella::card>