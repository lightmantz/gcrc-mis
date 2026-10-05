@php
    $diagnosis = $diagnosis ?? new \App\Models\Diagnosis();
    $editing = $diagnosis->exists;
@endphp

{{-- ─── Subject ──────────────────────────────────────── --}}
<div class="form-row">
    <div class="form-group">
        <label class="form-label" for="child_id">Child <span class="required">*</span></label>
        @if ($editing)
            <input type="text" class="form-control"
                   value="{{ $diagnosis->child->full_name }} ({{ $diagnosis->child->child_number }})"
                   readonly>
            <input type="hidden" name="child_id" value="{{ $diagnosis->child_id }}">
        @else
            <select id="child_id" name="child_id"
                    class="form-control @error('child_id') is-invalid @enderror" required>
                <option value="">— Select a child —</option>
                @foreach ($children as $c)
                    <option value="{{ $c->id }}"
                            @selected(old('child_id', $child?->id) == $c->id)>
                        {{ $c->full_name }} ({{ $c->child_number }})
                    </option>
                @endforeach
            </select>
            @error('child_id') <p class="form-error">{{ $message }}</p> @enderror
        @endif
    </div>

    <div class="form-group">
        <label class="form-label" for="diagnosed_by">Diagnosed by <span class="required">*</span></label>
        <select id="diagnosed_by" name="diagnosed_by"
                class="form-control @error('diagnosed_by') is-invalid @enderror" required>
            <option value="">— Select clinician —</option>
            @foreach ($staff as $s)
                <option value="{{ $s->id }}"
                        @selected(old('diagnosed_by', $diagnosis->diagnosed_by) == $s->id)>
                    {{ $s->full_name }} — {{ ucfirst($s->category) }}
                </option>
            @endforeach
        </select>
        @error('diagnosed_by') <p class="form-error">{{ $message }}</p> @enderror
    </div>
</div>

@if (isset($assessment))
    <input type="hidden" name="assessment_id" value="{{ $assessment->id }}">
    <div class="form-group">
        <label class="form-label">Linked assessment</label>
        <input type="text" class="form-control"
               value="{{ $assessment->type_label }} — {{ $assessment->assessment_date->format('d M Y') }}"
               readonly>
    </div>
@elseif ($editing && $diagnosis->assessment_id)
    <div class="form-group">
        <label class="form-label">Linked assessment</label>
        <input type="text" class="form-control"
               value="{{ $diagnosis->assessment->type_label }} — {{ $diagnosis->assessment->assessment_date->format('d M Y') }}"
               readonly>
    </div>
@endif

{{-- ─── Condition ────────────────────────────────────── --}}
<div class="form-group">
    <label class="form-label" for="condition_key">Condition <span class="required">*</span></label>
    <select id="condition_key" name="condition_key"
            class="form-control @error('condition_key') is-invalid @enderror" required>
        <option value="">— Select a condition —</option>
        @foreach ($vocabulary as $categoryLabel => $conditions)
            <optgroup label="{{ $categoryLabel }}">
                @foreach ($conditions as $key => $label)
                    <option value="{{ $key }}"
                            @selected(old('condition_key', $diagnosis->condition_key) === $key)>
                        {{ $label }}
                    </option>
                @endforeach
            </optgroup>
        @endforeach
    </select>
    @error('condition_key') <p class="form-error">{{ $message }}</p> @enderror
</div>

<div class="form-group" id="condition_other_wrap"
     style="{{ old('condition_key', $diagnosis->condition_key) === 'other' ? '' : 'display: none;' }}">
    <label class="form-label" for="condition_other">
        Specify condition <span class="required">*</span>
    </label>
    <input type="text" id="condition_other" name="condition_other"
           value="{{ old('condition_other', $diagnosis->condition_other) }}"
           class="form-control @error('condition_other') is-invalid @enderror">
    @error('condition_other') <p class="form-error">{{ $message }}</p> @enderror
</div>

{{-- ─── Clinical classification ──────────────────────── --}}
<div class="form-row cols-3">
    <div class="form-group">
        <label class="form-label" for="diagnosis_type">Diagnosis type <span class="required">*</span></label>
        <select id="diagnosis_type" name="diagnosis_type" class="form-control" required>
            <option value="primary" @selected(old('diagnosis_type', $diagnosis->diagnosis_type ?? 'primary') === 'primary')>Primary</option>
            <option value="secondary" @selected(old('diagnosis_type', $diagnosis->diagnosis_type) === 'secondary')>Secondary</option>
        </select>
    </div>

    <div class="form-group">
        <label class="form-label" for="severity">Severity <span class="required">*</span></label>
        <select id="severity" name="severity" class="form-control" required>
            @foreach (['unspecified' => 'Unspecified', 'mild' => 'Mild', 'moderate' => 'Moderate', 'severe' => 'Severe', 'profound' => 'Profound'] as $v => $l)
                <option value="{{ $v }}" @selected(old('severity', $diagnosis->severity ?? 'unspecified') === $v)>{{ $l }}</option>
            @endforeach
        </select>
    </div>

    <div class="form-group">
        <label class="form-label" for="status">Status <span class="required">*</span></label>
        <select id="status" name="status" class="form-control" required>
            @foreach (['active' => 'Active', 'resolved' => 'Resolved', 'ruled_out' => 'Ruled Out', 'transferred' => 'Transferred'] as $v => $l)
                <option value="{{ $v }}" @selected(old('status', $diagnosis->status ?? 'active') === $v)>{{ $l }}</option>
            @endforeach
        </select>
    </div>
</div>

{{-- ─── Dates ────────────────────────────────────────── --}}
<div class="form-row cols-3">
    <div class="form-group">
        <label class="form-label" for="diagnosed_at">Diagnosed on <span class="required">*</span></label>
        <input type="date" id="diagnosed_at" name="diagnosed_at"
               value="{{ old('diagnosed_at', $diagnosis->diagnosed_at?->toDateString() ?? now()->toDateString()) }}"
               max="{{ now()->toDateString() }}"
               class="form-control @error('diagnosed_at') is-invalid @enderror" required>
        @error('diagnosed_at') <p class="form-error">{{ $message }}</p> @enderror
    </div>

    <div class="form-group">
        <label class="form-label" for="ended_at">Ended on</label>
        <input type="date" id="ended_at" name="ended_at"
               value="{{ old('ended_at', $diagnosis->ended_at?->toDateString()) }}"
               class="form-control">
        <p class="form-help">Leave blank while the diagnosis is active.</p>
    </div>

    <div class="form-group">
        <label class="form-label" for="confirmed">Confirmed</label>
        <label class="form-check" style="margin-top: 8px;">
            <input type="checkbox" name="confirmed" value="1"
                   @checked(old('confirmed', $diagnosis->confirmed ?? true))>
            <span>Diagnosis is confirmed (not provisional)</span>
        </label>
    </div>
</div>

<div class="form-group">
    <label class="form-label" for="ended_reason">Ended reason</label>
    <input type="text" id="ended_reason" name="ended_reason"
           value="{{ old('ended_reason', $diagnosis->ended_reason) }}"
           class="form-control"
           placeholder="e.g. Symptoms resolved after therapy">
</div>

{{-- ─── Clinical notes ───────────────────────────────── --}}
<div class="form-group">
    <label class="form-label" for="notes">Clinical notes</label>
    <textarea id="notes" name="notes" class="form-control" rows="4">{{ old('notes', $diagnosis->notes) }}</textarea>
    <p class="form-help">Clinical reasoning, supporting evidence, or context for this diagnosis.</p>
</div>

{{-- Toggle "Other" field --}}
<script>
(function () {
    const select = document.getElementById('condition_key');
    const wrap = document.getElementById('condition_other_wrap');
    if (!select || !wrap) return;

    function toggle() {
        wrap.style.display = select.value === 'other' ? '' : 'none';
    }

    select.addEventListener('change', toggle);
    toggle();
})();
</script>