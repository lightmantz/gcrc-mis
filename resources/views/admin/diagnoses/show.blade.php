@extends('gentelella::page')

@section('title', $diagnosis->display_label)
@section('page_key', 'diagnoses')

@section('content')
    <x-gentelella::page-header title="{{ $diagnosis->display_label }}"
                                pretitle="{{ $diagnosis->child->full_name }} — {{ $diagnosis->diagnosed_at->format('d M Y') }}">
        <x-slot:actions>
            @can('update', $diagnosis)
                <a href="{{ route('admin.diagnoses.edit', $diagnosis) }}" class="btn btn-outline">Edit</a>
            @endcan
            @can('close', $diagnosis)
                <button type="button" class="btn btn-primary" onclick="document.getElementById('close-form').style.display = 'block'; this.style.display = 'none';">
                    Close Diagnosis
                </button>
            @endcan
        </x-slot:actions>
    </x-gentelella::page-header>

    @can('close', $diagnosis)
        <x-gentelella::card id="close-form" style="display: none; margin-bottom: 16px;">
            <form method="POST" action="{{ route('admin.diagnoses.close', $diagnosis) }}">
                @csrf

                <div class="form-row cols-3">
                    <div class="form-group">
                        <label class="form-label">Outcome <span class="required">*</span></label>
                        <select name="status" class="form-control" required>
                            <option value="resolved">Resolved</option>
                            <option value="ruled_out">Ruled Out</option>
                            <option value="transferred">Transferred</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Ended on <span class="required">*</span></label>
                        <input type="date" name="ended_at" class="form-control"
                               value="{{ now()->toDateString() }}"
                               min="{{ $diagnosis->diagnosed_at->toDateString() }}"
                               max="{{ now()->toDateString() }}" required>
                    </div>
                    <div class="form-group" style="display: flex; align-items: end;">
                        <button type="submit" class="btn btn-primary">Confirm</button>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Reason for closure <span class="required">*</span></label>
                    <textarea name="ended_reason" class="form-control" rows="2" required></textarea>
                </div>
            </form>
        </x-gentelella::card>
    @endcan

    <div class="row col-8-4">
        <div>
            <x-gentelella::card title="Diagnosis Details">
                <dl style="display: grid; grid-template-columns: auto 1fr; gap: 8px 16px; font-size: 13px;">
                    <dt style="color: var(--text-muted);">Child</dt>
                    <dd>
                        <a href="{{ route('admin.children.show', $diagnosis->child) }}">
                            {{ $diagnosis->child->full_name }}
                        </a>
                        <span style="color: var(--text-muted); font-size: 12px;">
                            ({{ $diagnosis->child->child_number }})
                        </span>
                    </dd>

                    <dt style="color: var(--text-muted);">Condition</dt>
                    <dd>{{ $diagnosis->display_label }}</dd>

                    <dt style="color: var(--text-muted);">Category</dt>
                    <dd>{{ $diagnosis->category_label ?? '—' }}</dd>

                    <dt style="color: var(--text-muted);">Diagnosis type</dt>
                    <dd>
                        <x-gentelella::badge :tone="$diagnosis->diagnosis_type_tone">
                            {{ $diagnosis->diagnosis_type_label }}
                        </x-gentelella::badge>
                    </dd>

                    <dt style="color: var(--text-muted);">Severity</dt>
                    <dd>
                        <x-gentelella::badge :tone="$diagnosis->severity_tone">
                            {{ $diagnosis->severity_label }}
                        </x-gentelella::badge>
                    </dd>

                    <dt style="color: var(--text-muted);">Status</dt>
                    <dd>
                        <x-gentelella::badge :tone="$diagnosis->status_tone">
                            {{ $diagnosis->status_label }}
                        </x-gentelella::badge>
                        @if ($diagnosis->isProvisional())
                            <x-gentelella::badge tone="yellow">Provisional — awaiting confirmation</x-gentelella::badge>
                        @endif
                    </dd>

                    <dt style="color: var(--text-muted);">Diagnosed by</dt>
                    <dd>{{ $diagnosis->diagnosedBy->full_name ?? '—' }}</dd>

                    <dt style="color: var(--text-muted);">Diagnosed on</dt>
                    <dd>
                        {{ $diagnosis->diagnosed_at->format('d M Y') }}
                        <span style="color: var(--text-muted); font-size: 12px;">
                            ({{ $diagnosis->diagnosed_at->diffForHumans() }})
                        </span>
                    </dd>

                    @if ($diagnosis->ended_at)
                        <dt style="color: var(--text-muted);">Ended on</dt>
                        <dd>
                            {{ $diagnosis->ended_at->format('d M Y') }}
                            @if ($diagnosis->duration_in_days)
                                <span style="color: var(--text-muted); font-size: 12px;">
                                    ({{ $diagnosis->duration_in_days }} days)
                                </span>
                            @endif
                        </dd>

                        @if ($diagnosis->ended_reason)
                            <dt style="color: var(--text-muted);">Ended reason</dt>
                            <dd>{{ $diagnosis->ended_reason }}</dd>
                        @endif
                    @endif

                    @if ($diagnosis->assessment)
                        <dt style="color: var(--text-muted);">Source assessment</dt>
                        <dd>
                            <a href="{{ route('admin.assessments.show', $diagnosis->assessment) }}">
                                {{ $diagnosis->assessment->type_label }} —
                                {{ $diagnosis->assessment->assessment_date->format('d M Y') }}
                            </a>
                        </dd>
                    @endif
                </dl>
            </x-gentelella::card>

            @if ($diagnosis->notes)
                <x-gentelella::card title="Clinical Notes" style="margin-top: 16px;">
                    <p style="white-space: pre-line; font-size: 13px;">{{ $diagnosis->notes }}</p>
                </x-gentelella::card>
            @endif
        </div>

        <div>
            <x-gentelella::card title="Other Diagnoses for {{ $diagnosis->child->first_name }}">
                @forelse ($siblingDiagnoses as $sibling)
                    <div style="padding: 8px 0; border-bottom: 1px solid var(--border-color-light);">
                        <a href="{{ route('admin.diagnoses.show', $sibling) }}" style="font-size: 13px; font-weight: 500;">
                            {{ $sibling->display_label }}
                        </a>
                        <div style="font-size: 11.5px; color: var(--text-muted); margin-top: 2px;">
                            <x-gentelella::badge :tone="$sibling->diagnosis_type_tone">
                                {{ $sibling->diagnosis_type_label }}
                            </x-gentelella::badge>
                            <x-gentelella::badge :tone="$sibling->status_tone">
                                {{ $sibling->status_label }}
                            </x-gentelella::badge>
                            — {{ $sibling->diagnosed_at->format('d M Y') }}
                        </div>
                    </div>
                @empty
                    <p style="color: var(--text-muted); font-size: 13px;">
                        No other diagnoses for this child.
                    </p>
                @endforelse
            </x-gentelella::card>

            @can('delete', $diagnosis)
                <x-gentelella::card title="Danger Zone" style="margin-top: 16px;">
                    <form method="POST" action="{{ route('admin.diagnoses.destroy', $diagnosis) }}"
                          onsubmit="return confirm('Delete this diagnosis? Only provisional or closed diagnoses can be deleted.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete Diagnosis</button>
                    </form>
                </x-gentelella::card>
            @endcan
        </div>
    </div>
@endsection