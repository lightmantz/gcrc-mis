@php
    $assessment = $assessment ?? null;
@endphp

<div class="form-row">
    <div class="form-group">
        <label class="form-label" for="child_id">Child <span class="required">*</span></label>
        @if ($assessment)
            {{-- Child is immutable on edit --}}
            <input type="text" class="form-control"
                   value="{{ $assessment->child->full_name }} ({{ $assessment->child->child_number }})"
                   readonly>
            <input type="hidden" name="child_id" value="{{ $assessment->child_id }}">
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
        <label class="form-label" for="assessment_date">Assessment date <span class="required">*</span></label>
        <input type="date" id="assessment_date" name="assessment_date"
               value="{{ old('assessment_date', $assessment?->assessment_date?->toDateString() ?? now()->toDateString()) }}"
               max="{{ now()->toDateString() }}"
               class="form-control @error('assessment_date') is-invalid @enderror" required>
        @error('assessment_date') <p class="form-error">{{ $message }}</p> @enderror
    </div>
</div>

@if ($showTypeSelect)
    <div class="form-group">
        <label class="form-label" for="type">Assessment type <span class="required">*</span></label>
        <select id="type" name="type"
                class="form-control @error('type') is-invalid @enderror" required>
            <option value="">— Choose an assessment type —</option>
            @foreach ($types as $key => $label)
                <option value="{{ $key }}" @selected(old('type') === $key)>{{ $label }}</option>
            @endforeach
        </select>
        @error('type') <p class="form-error">{{ $message }}</p> @enderror
        <p class="form-help" id="type-description"></p>
    </div>
@else
    <div class="form-group">
        <label class="form-label">Assessment type</label>
        <input type="text" class="form-control" value="{{ $assessment->type_label }}" readonly>
    </div>
@endif

<div class="form-group">
    <label class="form-label" for="assessor_id">Assessor <span class="required">*</span></label>
    <select id="assessor_id" name="assessor_id"
            class="form-control @error('assessor_id') is-invalid @enderror" required>
        <option value="">— Select assessor —</option>
        @foreach ($staff as $s)
            <option value="{{ $s->id }}"
                    data-category="{{ $s->category }}"
                    @selected(old('assessor_id', $assessment?->assessor_id) == $s->id)>
                {{ $s->full_name }} — {{ ucfirst($s->category) }}
            </option>
        @endforeach
    </select>
    @error('assessor_id') <p class="form-error">{{ $message }}</p> @enderror
</div>