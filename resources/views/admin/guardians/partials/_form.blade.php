@php
    $guardian = $guardian ?? new \App\Models\Guardian();
@endphp

<h3 style="font-size: 12px; text-transform: uppercase; letter-spacing: 0.4px; color: var(--text-muted); margin: 0 0 12px;">
    Identity
</h3>

<div class="form-row">
    <div class="form-group">
        <label class="form-label" for="first_name">First name <span class="required">*</span></label>
        <input type="text" id="first_name" name="first_name"
               value="{{ old('first_name', $guardian->first_name) }}"
               class="form-control @error('first_name') is-invalid @enderror" required>
        @error('first_name') <p class="form-error">{{ $message }}</p> @enderror
    </div>

    <div class="form-group">
        <label class="form-label" for="middle_name">Middle name</label>
        <input type="text" id="middle_name" name="middle_name"
               value="{{ old('middle_name', $guardian->middle_name) }}"
               class="form-control">
    </div>

    <div class="form-group">
        <label class="form-label" for="last_name">Last name <span class="required">*</span></label>
        <input type="text" id="last_name" name="last_name"
               value="{{ old('last_name', $guardian->last_name) }}"
               class="form-control @error('last_name') is-invalid @enderror" required>
        @error('last_name') <p class="form-error">{{ $message }}</p> @enderror
    </div>
</div>

<div class="form-row">
    <div class="form-group">
        <label class="form-label" for="preferred_name">Preferred name</label>
        <input type="text" id="preferred_name" name="preferred_name"
               value="{{ old('preferred_name', $guardian->preferred_name) }}"
               class="form-control">
    </div>

    <div class="form-group">
        <label class="form-label" for="date_of_birth">Date of birth</label>
        <input type="date" id="date_of_birth" name="date_of_birth"
               value="{{ old('date_of_birth', $guardian->date_of_birth?->toDateString()) }}"
               class="form-control">
    </div>

    <div class="form-group">
        <label class="form-label" for="gender">Gender</label>
        <select id="gender" name="gender" class="form-control">
            <option value="">—</option>
            @foreach (['male' => 'Male', 'female' => 'Female', 'other' => 'Other'] as $v => $l)
                <option value="{{ $v }}" @selected(old('gender', $guardian->gender) === $v)>{{ $l }}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="form-row">
    <div class="form-group">
        <label class="form-label" for="national_id">National ID</label>
        <input type="text" id="national_id" name="national_id"
               value="{{ old('national_id', $guardian->national_id) }}"
               class="form-control @error('national_id') is-invalid @enderror">
        @error('national_id') <p class="form-error">{{ $message }}</p> @enderror
    </div>
</div>

<h3 style="font-size: 12px; text-transform: uppercase; letter-spacing: 0.4px; color: var(--text-muted); margin: 24px 0 12px;">
    Contact
</h3>

<div class="form-row">
    <div class="form-group">
        <label class="form-label" for="phone">Phone <span class="required">*</span></label>
        <input type="text" id="phone" name="phone"
               value="{{ old('phone', $guardian->phone) }}"
               class="form-control @error('phone') is-invalid @enderror" required>
        @error('phone') <p class="form-error">{{ $message }}</p> @enderror
    </div>

    <div class="form-group">
        <label class="form-label" for="alternate_phone">Alternate phone</label>
        <input type="text" id="alternate_phone" name="alternate_phone"
               value="{{ old('alternate_phone', $guardian->alternate_phone) }}"
               class="form-control">
    </div>

    <div class="form-group">
        <label class="form-label" for="email">Email</label>
        <input type="email" id="email" name="email"
               value="{{ old('email', $guardian->email) }}"
               class="form-control">
    </div>
</div>

<div class="form-group">
    <label class="form-label" for="address">Address</label>
    <textarea id="address" name="address" class="form-control" rows="2">{{ old('address', $guardian->address) }}</textarea>
</div>

<div class="form-row">
    <div class="form-group">
        <label class="form-label" for="district">District</label>
        <input type="text" id="district" name="district"
               value="{{ old('district', $guardian->district) }}" class="form-control">
    </div>

    <div class="form-group">
        <label class="form-label" for="region">Region</label>
        <input type="text" id="region" name="region"
               value="{{ old('region', $guardian->region) }}" class="form-control">
    </div>
</div>

<h3 style="font-size: 12px; text-transform: uppercase; letter-spacing: 0.4px; color: var(--text-muted); margin: 24px 0 12px;">
    Background
</h3>

<div class="form-row">
    <div class="form-group">
        <label class="form-label" for="occupation">Occupation</label>
        <input type="text" id="occupation" name="occupation"
               value="{{ old('occupation', $guardian->occupation) }}" class="form-control">
    </div>

    <div class="form-group">
        <label class="form-label" for="employer">Employer</label>
        <input type="text" id="employer" name="employer"
               value="{{ old('employer', $guardian->employer) }}" class="form-control">
    </div>

    <div class="form-group">
        <label class="form-label" for="education_level">Education level</label>
        <input type="text" id="education_level" name="education_level"
               value="{{ old('education_level', $guardian->education_level) }}" class="form-control">
    </div>
</div>

<div class="form-group">
    <label class="form-label" for="notes">Internal notes</label>
    <textarea id="notes" name="notes" class="form-control" rows="2">{{ old('notes', $guardian->notes) }}</textarea>
</div>