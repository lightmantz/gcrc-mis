@php
    $plan = $plan ?? new \App\Models\TreatmentPlan();
    $editing = $plan->exists;
    $selectedDiagnosisIds = old('diagnosis_ids', $editing ? $plan->diagnoses->pluck('id')->toArray() : []);
    $teamMembers = old('team_members', $editing
        ? $plan->teamMembers->map(fn ($m) => ['staff_id' => $m->id, 'role' => $m->pivot->role])->toArray()
        : []);
@endphp

{{-- ─── Subject & Discipline ──────────────────────────── --}}
<h3 style="font-size: 12px; text-transform: uppercase; letter-spacing: 0.4px; color: var(--text-muted); margin: 0 0 12px;">
    Subject
</h3>

<div class="form-row">
    <div class="form-group">
        <label class="form-label" for="child_id">Child <span class="required">*</span></label>
        @if ($editing)
            <input type="text" class="form-control"
                   value="{{ $plan->child->full_name }} ({{ $plan->child->child_number }})" readonly>
            <input type="hidden" name="child_id" value="{{ $plan->child_id }}">
        @else
            <select id="child_id" name="child_id" class="form-control @error('child_id') is-invalid @enderror" required>
                <option value="">— Select a child —</option>
                @foreach ($children as $c)
                    <option value="{{ $c->id }}" @selected(old('child_id', $child?->id) == $c->id)>
                        {{ $c->full_name }} ({{ $c->child_number }})
                    </option>
                @endforeach
            </select>
            @error('child_id') <p class="form-error">{{ $message }}</p> @enderror
        @endif
    </div>

    <div class="form-group">
        <label class="form-label" for="discipline">Discipline <span class="required">*</span></label>
        @if ($editing)
            <input type="text" class="form-control" value="{{ $plan->discipline_label }}" readonly>
        @else
            <select id="discipline" name="discipline" class="form-control @error('discipline') is-invalid @enderror" required>
                <option value="">—</option>
                @foreach (['physiotherapy' => 'Physiotherapy', 'occupational_therapy' => 'Occupational Therapy', 'speech' => 'Speech & Language', 'psychology' => 'Psychology', 'social_work' => 'Social Work', 'education' => 'Education', 'multi_disciplinary' => 'Multi-Disciplinary'] as $v => $l)
                    <option value="{{ $v }}" @selected(old('discipline', $plan->discipline) === $v)>{{ $l }}</option>
                @endforeach
            </select>
            @error('discipline') <p class="form-error">{{ $message }}</p> @enderror
        @endif
    </div>

    <div class="form-group">
        <label class="form-label" for="lead_staff_id">Lead clinician <span class="required">*</span></label>
        <select id="lead_staff_id" name="lead_staff_id" class="form-control @error('lead_staff_id') is-invalid @enderror" required>
            <option value="">— Select clinician —</option>
            @foreach ($staff as $s)
                <option value="{{ $s->id }}" @selected(old('lead_staff_id', $plan->lead_staff_id) == $s->id)>
                    {{ $s->full_name }} — {{ $s->category_label }}
                </option>
            @endforeach
        </select>
        @error('lead_staff_id') <p class="form-error">{{ $message }}</p> @enderror
    </div>
</div>

{{-- ─── Diagnoses ─────────────────────────────────────── --}}
<h3 style="font-size: 12px; text-transform: uppercase; letter-spacing: 0.4px; color: var(--text-muted); margin: 24px 0 12px;">
    Diagnoses Addressed
</h3>

@if ($diagnoses->isEmpty())
    <p style="color: var(--text-muted); font-size: 13px;">
        This child has no active diagnoses. You can still create the plan, but linking it to a diagnosis is recommended.
    </p>
@else
    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 6px; padding: 12px; border: 1px solid var(--border-color); border-radius: var(--radius-sm);">
        @foreach ($diagnoses as $diagnosis)
            <label class="form-check">
                <input type="checkbox" name="diagnosis_ids[]" value="{{ $diagnosis->id }}"
                       @checked(in_array($diagnosis->id, $selectedDiagnosisIds))>
                <span>
                    <strong>{{ $diagnosis->display_label }}</strong>
                    <span style="color: var(--text-muted); font-size: 11.5px;">
                        ({{ $diagnosis->diagnosis_type_label }}, {{ $diagnosis->severity_label }})
                    </span>
                </span>
            </label>
        @endforeach
    </div>
@endif

{{-- ─── Dates & Review ─────────────────────────────────── --}}
<h3 style="font-size: 12px; text-transform: uppercase; letter-spacing: 0.4px; color: var(--text-muted); margin: 24px 0 12px;">
    Timeline & Review
</h3>

<div class="form-row cols-3">
    <div class="form-group">
        <label class="form-label" for="start_date">Start date <span class="required">*</span></label>
        <input type="date" id="start_date" name="start_date"
               value="{{ old('start_date', $plan->start_date?->toDateString() ?? now()->toDateString()) }}"
               class="form-control @error('start_date') is-invalid @enderror" required>
        @error('start_date') <p class="form-error">{{ $message }}</p> @enderror
    </div>

    <div class="form-group">
        <label class="form-label" for="target_review_date">Target review date</label>
        <input type="date" id="target_review_date" name="target_review_date"
               value="{{ old('target_review_date', $plan->target_review_date?->toDateString()) }}"
               class="form-control">
    </div>

    <div class="form-group">
        <label class="form-label" for="end_date">Target end date</label>
        <input type="date" id="end_date" name="end_date"
               value="{{ old('end_date', $plan->end_date?->toDateString()) }}"
               class="form-control">
    </div>
</div>

<div class="form-row cols-3">
    <div class="form-group">
        <label class="form-label" for="review_cycle">Review cycle <span class="required">*</span></label>
        <select id="review_cycle" name="review_cycle" class="form-control" required>
            @foreach (['weekly' => 'Weekly', 'biweekly' => 'Every 2 weeks', 'monthly' => 'Monthly', 'quarterly' => 'Quarterly', 'ad_hoc' => 'As needed'] as $v => $l)
                <option value="{{ $v }}" @selected(old('review_cycle', $plan->review_cycle ?? 'monthly') === $v)>{{ $l }}</option>
            @endforeach
        </select>
    </div>

    <div class="form-group">
        <label class="form-label" for="next_review_date">Next review date</label>
        <input type="date" id="next_review_date" name="next_review_date"
               value="{{ old('next_review_date', $plan->next_review_date?->toDateString()) }}"
               class="form-control">
        <p class="form-help">The date the plan is due for its next formal review.</p>
    </div>
</div>

{{-- ─── Team Members ───────────────────────────────────── --}}
<h3 style="font-size: 12px; text-transform: uppercase; letter-spacing: 0.4px; color: var(--text-muted); margin: 24px 0 12px;">
    Contributing Team Members
</h3>

<div id="team-container">
    @foreach ($teamMembers as $index => $member)
        <div class="form-row" style="margin-bottom: 8px;">
            <div class="form-group">
                <select name="team_members[{{ $index }}][staff_id]" class="form-control">
                    <option value="">— Select staff —</option>
                    @foreach ($staff as $s)
                        <option value="{{ $s->id }}" @selected(($member['staff_id'] ?? null) == $s->id)>
                            {{ $s->full_name }} — {{ $s->category_label }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <input type="text" name="team_members[{{ $index }}][role]" class="form-control"
                       value="{{ $member['role'] ?? '' }}"
                       placeholder="Role (e.g. Physiotherapist, Nurse)">
            </div>
        </div>
    @endforeach
</div>

<button type="button" class="btn btn-outline btn-sm" id="add-team-member">+ Add Team Member</button>

{{-- ─── Content ────────────────────────────────────────── --}}
<h3 style="font-size: 12px; text-transform: uppercase; letter-spacing: 0.4px; color: var(--text-muted); margin: 24px 0 12px;">
    Plan Content
</h3>

<div class="form-group">
    <label class="form-label" for="overall_objectives">Overall objectives</label>
    <textarea id="overall_objectives" name="overall_objectives" class="form-control" rows="4"
              placeholder="What this plan aims to achieve overall.">{{ old('overall_objectives', $plan->overall_objectives) }}</textarea>
</div>

<div class="form-group">
    <label class="form-label" for="notes">Notes</label>
    <textarea id="notes" name="notes" class="form-control" rows="3">{{ old('notes', $plan->notes) }}</textarea>
</div>

<script>
(function () {
    const container = document.getElementById('team-container');
    const addBtn = document.getElementById('add-team-member');
    const staffOptions = @json($staff->map(fn ($s) => ['id' => $s->id, 'label' => $s->full_name . ' — ' . $s->category_label])->values());

    if (!container || !addBtn) return;

    let counter = container.querySelectorAll('.form-row').length;

    addBtn.addEventListener('click', () => {
        const row = document.createElement('div');
        row.className = 'form-row';
        row.style.marginBottom = '8px';

        const options = staffOptions.map((s) => `<option value="${s.id}">${s.label}</option>`).join('');

        row.innerHTML = `
            <div class="form-group">
                <select name="team_members[${counter}][staff_id]" class="form-control">
                    <option value="">— Select staff —</option>
                    ${options}
                </select>
            </div>
            <div class="form-group">
                <input type="text" name="team_members[${counter}][role]" class="form-control" placeholder="Role">
            </div>
        `;

        container.appendChild(row);
        counter++;
    });
})();
</script>