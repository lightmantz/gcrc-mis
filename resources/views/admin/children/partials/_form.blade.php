@php
    $child = $child ?? new \App\Models\Child();
    $editing = $child->exists;
    $canSeeMedical = auth()->user()->can('children.view_medical');
@endphp

{{-- ─── Identity ──────────────────────────────────────── --}}
<h3 style="font-size: 12px; text-transform: uppercase; letter-spacing: 0.4px; color: var(--text-muted); margin: 0 0 12px;">
    Identity
</h3>

<div class="form-row">
    <div class="form-group">
        <label class="form-label" for="first_name">First name <span class="required">*</span></label>
        <input type="text" id="first_name" name="first_name"
               value="{{ old('first_name', $child->first_name) }}"
               class="form-control @error('first_name') is-invalid @enderror" required>
        @error('first_name') <p class="form-error">{{ $message }}</p> @enderror
    </div>

    <div class="form-group">
        <label class="form-label" for="middle_name">Middle name</label>
        <input type="text" id="middle_name" name="middle_name"
               value="{{ old('middle_name', $child->middle_name) }}"
               class="form-control">
    </div>

    <div class="form-group">
        <label class="form-label" for="last_name">Last name <span class="required">*</span></label>
        <input type="text" id="last_name" name="last_name"
               value="{{ old('last_name', $child->last_name) }}"
               class="form-control @error('last_name') is-invalid @enderror" required>
        @error('last_name') <p class="form-error">{{ $message }}</p> @enderror
    </div>
</div>

<div class="form-row">
    <div class="form-group">
        <label class="form-label" for="preferred_name">Preferred name</label>
        <input type="text" id="preferred_name" name="preferred_name"
               value="{{ old('preferred_name', $child->preferred_name) }}"
               class="form-control">
    </div>

    <div class="form-group">
        <label class="form-label" for="date_of_birth">Date of birth <span class="required">*</span></label>
        <input type="date" id="date_of_birth" name="date_of_birth"
               value="{{ old('date_of_birth', $child->date_of_birth?->toDateString()) }}"
               max="{{ now()->toDateString() }}"
               class="form-control @error('date_of_birth') is-invalid @enderror" required>
        @error('date_of_birth') <p class="form-error">{{ $message }}</p> @enderror
    </div>

    <div class="form-group">
        <label class="form-label" for="gender">Gender <span class="required">*</span></label>
        <select id="gender" name="gender"
                class="form-control @error('gender') is-invalid @enderror" required>
            <option value="">—</option>
            @foreach (['male' => 'Male', 'female' => 'Female', 'other' => 'Other'] as $v => $l)
                <option value="{{ $v }}" @selected(old('gender', $child->gender) === $v)>{{ $l }}</option>
            @endforeach
        </select>
        @error('gender') <p class="form-error">{{ $message }}</p> @enderror
    </div>
</div>

{{-- ─── Contact ──────────────────────────────────────── --}}
<h3 style="font-size: 12px; text-transform: uppercase; letter-spacing: 0.4px; color: var(--text-muted); margin: 24px 0 12px;">
    Contact
</h3>

<div class="form-row">
    <div class="form-group">
        <label class="form-label" for="phone">Phone</label>
        <input type="text" id="phone" name="phone"
               value="{{ old('phone', $child->phone) }}"
               class="form-control">
    </div>

    <div class="form-group">
        <label class="form-label" for="email">Email</label>
        <input type="email" id="email" name="email"
               value="{{ old('email', $child->email) }}"
               class="form-control">
    </div>
</div>

<div class="form-group">
    <label class="form-label" for="address">Address</label>
    <textarea id="address" name="address" class="form-control" rows="2">{{ old('address', $child->address) }}</textarea>
</div>

<div class="form-row">
    <div class="form-group">
        <label class="form-label" for="district">District</label>
        <input type="text" id="district" name="district"
               value="{{ old('district', $child->district) }}"
               class="form-control">
    </div>

    <div class="form-group">
        <label class="form-label" for="region">Region</label>
        <input type="text" id="region" name="region"
               value="{{ old('region', $child->region) }}"
               class="form-control">
    </div>
</div>

{{-- ─── Medical ──────────────────────────────────────── --}}
@if ($canSeeMedical)
    <h3 style="font-size: 12px; text-transform: uppercase; letter-spacing: 0.4px; color: var(--text-muted); margin: 24px 0 12px;">
        Medical
    </h3>

    <div class="form-row">
        <div class="form-group">
            <label class="form-label" for="blood_type">Blood type</label>
            <select id="blood_type" name="blood_type" class="form-control">
                @foreach (['unknown' => 'Unknown', 'A+' => 'A+', 'A-' => 'A-', 'B+' => 'B+', 'B-' => 'B-', 'AB+' => 'AB+', 'AB-' => 'AB-', 'O+' => 'O+', 'O-' => 'O-'] as $v => $l)
                    <option value="{{ $v }}" @selected(old('blood_type', $child->blood_type ?? 'unknown') === $v)>{{ $l }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label class="form-label" for="primary_condition">Primary condition</label>
            <select id="primary_condition" name="primary_condition"
                    class="form-control @error('primary_condition') is-invalid @enderror">
                <option value="">—</option>
                @foreach ([
                    'cerebral_palsy' => 'Cerebral Palsy',
                    'down_syndrome' => 'Down Syndrome',
                    'autism_spectrum' => 'Autism Spectrum Disorder',
                    'intellectual_disability' => 'Intellectual Disability',
                    'physical_disability' => 'Physical Disability',
                    'hearing_impairment' => 'Hearing Impairment',
                    'visual_impairment' => 'Visual Impairment',
                    'speech_language_disorder' => 'Speech / Language Disorder',
                    'learning_disability' => 'Learning Disability',
                    'multiple_disabilities' => 'Multiple Disabilities',
                    'other' => 'Other',
                ] as $v => $l)
                    <option value="{{ $v }}" @selected(old('primary_condition', $child->primary_condition) === $v)>{{ $l }}</option>
                @endforeach
            </select>
            @error('primary_condition') <p class="form-error">{{ $message }}</p> @enderror
        </div>
    </div>

    <div class="form-group" id="primary_condition_other_wrap"
         style="{{ old('primary_condition', $child->primary_condition) === 'other' ? '' : 'display: none;' }}">
        <label class="form-label" for="primary_condition_other">
            Specify "Other" condition <span class="required">*</span>
        </label>
        <input type="text" id="primary_condition_other" name="primary_condition_other"
               value="{{ old('primary_condition_other', $child->primary_condition_other) }}"
               class="form-control @error('primary_condition_other') is-invalid @enderror">
        @error('primary_condition_other') <p class="form-error">{{ $message }}</p> @enderror
    </div>

    <div class="form-group">
        <label class="form-label" for="disability_summary">Disability summary</label>
        <textarea id="disability_summary" name="disability_summary"
                  class="form-control @error('disability_summary') is-invalid @enderror"
                  rows="3"
                  placeholder="Brief narrative describing the child's condition and needs.">{{ old('disability_summary', $child->disability_summary) }}</textarea>
        @error('disability_summary') <p class="form-error">{{ $message }}</p> @enderror
    </div>

    <div class="form-group">
        <label class="form-label" for="allergies">Allergies</label>
        <textarea id="allergies" name="allergies" class="form-control" rows="2">{{ old('allergies', $child->allergies) }}</textarea>
    </div>

    <div class="form-group">
        <label class="form-label" for="chronic_conditions">Chronic conditions</label>
        <textarea id="chronic_conditions" name="chronic_conditions" class="form-control" rows="2">{{ old('chronic_conditions', $child->chronic_conditions) }}</textarea>
    </div>

    <div class="form-group">
        <label class="form-label" for="current_medications">Current medications</label>
        <textarea id="current_medications" name="current_medications" class="form-control" rows="2">{{ old('current_medications', $child->current_medications) }}</textarea>
    </div>
@else
    {{-- Non-medical users still need to be able to submit the form,
         so we send the existing values back hidden. --}}
    <input type="hidden" name="blood_type" value="{{ old('blood_type', $child->blood_type ?? 'unknown') }}">
    <input type="hidden" name="primary_condition" value="{{ old('primary_condition', $child->primary_condition) }}">
    <input type="hidden" name="primary_condition_other" value="{{ old('primary_condition_other', $child->primary_condition_other) }}">
    <input type="hidden" name="disability_summary" value="{{ old('disability_summary', $child->disability_summary) }}">
    <input type="hidden" name="allergies" value="{{ old('allergies', $child->allergies) }}">
    <input type="hidden" name="chronic_conditions" value="{{ old('chronic_conditions', $child->chronic_conditions) }}">
    <input type="hidden" name="current_medications" value="{{ old('current_medications', $child->current_medications) }}">

    <div class="alert alert-info" style="margin: 24px 0;">
        <div class="alert-body">
            <strong>Medical information</strong> is not shown here. Only users
            with medical-record access can view or edit the clinical fields.
        </div>
    </div>
@endif

{{-- ─── Special Care ─────────────────────────────────── --}}
<h3 style="font-size: 12px; text-transform: uppercase; letter-spacing: 0.4px; color: var(--text-muted); margin: 24px 0 12px;">
    Special Care Requirements
</h3>

<div class="form-group">
    <label class="form-label" for="special_care_requirements">Special care requirements</label>
    <textarea id="special_care_requirements" name="special_care_requirements"
              class="form-control" rows="3">{{ old('special_care_requirements', $child->special_care_requirements) }}</textarea>
</div>

<div class="form-row">
    <div class="form-group">
        <label class="form-label" for="feeding_requirements">Feeding requirements</label>
        <textarea id="feeding_requirements" name="feeding_requirements"
                  class="form-control" rows="2">{{ old('feeding_requirements', $child->feeding_requirements) }}</textarea>
    </div>

    <div class="form-group">
        <label class="form-label" for="mobility_notes">Mobility notes</label>
        <textarea id="mobility_notes" name="mobility_notes"
                  class="form-control" rows="2">{{ old('mobility_notes', $child->mobility_notes) }}</textarea>
    </div>
</div>

<div class="form-group">
    <label class="form-label" for="communication_notes">Communication notes</label>
    <textarea id="communication_notes" name="communication_notes"
              class="form-control" rows="2">{{ old('communication_notes', $child->communication_notes) }}</textarea>
</div>

<div class="form-group">
    <label class="form-check">
        <input type="checkbox" name="requires_constant_supervision" value="1"
               @checked(old('requires_constant_supervision', $child->requires_constant_supervision))>
        <span>Requires constant supervision</span>
    </label>
</div>

{{-- ─── Administrative ──────────────────────────────── --}}
<h3 style="font-size: 12px; text-transform: uppercase; letter-spacing: 0.4px; color: var(--text-muted); margin: 24px 0 12px;">
    Administrative
</h3>

<div class="form-row">
    <div class="form-group">
        <label class="form-label" for="registration_date">Registration date <span class="required">*</span></label>
        <input type="date" id="registration_date" name="registration_date"
               value="{{ old('registration_date', $child->registration_date?->toDateString() ?? now()->toDateString()) }}"
               class="form-control" required>
    </div>

    <div class="form-group">
        <label class="form-label" for="referred_by">Referred by</label>
        <input type="text" id="referred_by" name="referred_by"
               value="{{ old('referred_by', $child->referred_by) }}"
               class="form-control"
               placeholder="Person, clinic, or organization">
    </div>

    <div class="form-group">
        <label class="form-label" for="status">Status <span class="required">*</span></label>
        <select id="status" name="status" class="form-control" required>
            @foreach (['active' => 'Active', 'on_hold' => 'On Hold', 'discharged' => 'Discharged', 'deceased' => 'Deceased'] as $v => $l)
                <option value="{{ $v }}" @selected(old('status', $child->status ?? 'active') === $v)>{{ $l }}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="form-group">
    <label class="form-label" for="notes">Internal notes</label>
    <textarea id="notes" name="notes" class="form-control" rows="3">{{ old('notes', $child->notes) }}</textarea>
    <p class="form-help">Visible only to system users, not to guardians.</p>
</div>

{{-- Small script to reveal the "Other" text field --}}
<script>
(function () {
    const select = document.getElementById('primary_condition');
    const wrap = document.getElementById('primary_condition_other_wrap');
    if (!select || !wrap) return;

    function toggle() {
        wrap.style.display = select.value === 'other' ? '' : 'none';
    }

    select.addEventListener('change', toggle);
    toggle();
})();
</script>