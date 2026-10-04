@php
    $editing = isset($staff);
    $selectedUserIds = old('user_ids', $editing ? $staff->users->pluck('id')->toArray() : []);
@endphp

{{-- ─── Identity ──────────────────────────────────────── --}}
<div class="form-row">
    <div class="form-group">
        <label class="form-label" for="first_name">First name <span class="required">*</span></label>
        <input type="text" id="first_name" name="first_name"
               value="{{ old('first_name', $staff->first_name ?? '') }}"
               class="form-control @error('first_name') is-invalid @enderror" required>
        @error('first_name') <p class="form-error">{{ $message }}</p> @enderror
    </div>

    <div class="form-group">
        <label class="form-label" for="middle_name">Middle name</label>
        <input type="text" id="middle_name" name="middle_name"
               value="{{ old('middle_name', $staff->middle_name ?? '') }}"
               class="form-control">
    </div>

    <div class="form-group">
        <label class="form-label" for="last_name">Last name <span class="required">*</span></label>
        <input type="text" id="last_name" name="last_name"
               value="{{ old('last_name', $staff->last_name ?? '') }}"
               class="form-control @error('last_name') is-invalid @enderror" required>
        @error('last_name') <p class="form-error">{{ $message }}</p> @enderror
    </div>
</div>

<div class="form-row">
    <div class="form-group">
        <label class="form-label" for="preferred_name">Preferred name</label>
        <input type="text" id="preferred_name" name="preferred_name"
               value="{{ old('preferred_name', $staff->preferred_name ?? '') }}"
               class="form-control">
    </div>

    <div class="form-group">
        <label class="form-label" for="date_of_birth">Date of birth</label>
        <input type="date" id="date_of_birth" name="date_of_birth"
               value="{{ old('date_of_birth', $staff->date_of_birth?->toDateString() ?? '') }}"
               class="form-control">
    </div>

    <div class="form-group">
        <label class="form-label" for="gender">Gender</label>
        <select id="gender" name="gender" class="form-control">
            <option value="">—</option>
            @foreach (['male' => 'Male', 'female' => 'Female', 'other' => 'Other', 'prefer_not_to_say' => 'Prefer not to say'] as $v => $l)
                <option value="{{ $v }}" @selected(old('gender', $staff->gender ?? '') === $v)>{{ $l }}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="form-row">
    <div class="form-group">
        <label class="form-label" for="national_id">National ID</label>
        <input type="text" id="national_id" name="national_id"
               value="{{ old('national_id', $staff->national_id ?? '') }}"
               class="form-control @error('national_id') is-invalid @enderror">
        @error('national_id') <p class="form-error">{{ $message }}</p> @enderror
    </div>
</div>

{{-- ─── Contact ──────────────────────────────────────── --}}
<div class="form-row">
    <div class="form-group">
        <label class="form-label" for="phone">Phone <span class="required">*</span></label>
        <input type="text" id="phone" name="phone"
               value="{{ old('phone', $staff->phone ?? '') }}"
               class="form-control @error('phone') is-invalid @enderror" required>
        @error('phone') <p class="form-error">{{ $message }}</p> @enderror
    </div>

    <div class="form-group">
        <label class="form-label" for="email">Email</label>
        <input type="email" id="email" name="email"
               value="{{ old('email', $staff->email ?? '') }}"
               class="form-control @error('email') is-invalid @enderror">
        @error('email') <p class="form-error">{{ $message }}</p> @enderror
    </div>
</div>

<div class="form-group">
    <label class="form-label" for="address">Address</label>
    <textarea id="address" name="address" class="form-control" rows="2">{{ old('address', $staff->address ?? '') }}</textarea>
</div>

{{-- ─── Emergency Contact ────────────────────────────── --}}
<div class="form-row cols-3">
    <div class="form-group">
        <label class="form-label" for="emergency_contact_name">Emergency contact name</label>
        <input type="text" id="emergency_contact_name" name="emergency_contact_name"
               value="{{ old('emergency_contact_name', $staff->emergency_contact_name ?? '') }}"
               class="form-control">
    </div>

    <div class="form-group">
        <label class="form-label" for="emergency_contact_phone">Emergency contact phone</label>
        <input type="text" id="emergency_contact_phone" name="emergency_contact_phone"
               value="{{ old('emergency_contact_phone', $staff->emergency_contact_phone ?? '') }}"
               class="form-control">
    </div>

    <div class="form-group">
        <label class="form-label" for="emergency_contact_relation">Relation</label>
        <input type="text" id="emergency_contact_relation" name="emergency_contact_relation"
               value="{{ old('emergency_contact_relation', $staff->emergency_contact_relation ?? '') }}"
               class="form-control">
    </div>
</div>

{{-- ─── Employment ───────────────────────────────────── --}}
<div class="form-row">
    <div class="form-group">
        <label class="form-label" for="category">Category <span class="required">*</span></label>
        <select id="category" name="category" class="form-control @error('category') is-invalid @enderror" required>
            <option value="">—</option>
            @foreach (['management' => 'Management', 'clinical' => 'Clinical', 'therapy' => 'Therapy', 'education' => 'Education', 'admin' => 'Administration', 'support' => 'Support'] as $v => $l)
                <option value="{{ $v }}" @selected(old('category', $staff->category ?? '') === $v)>{{ $l }}</option>
            @endforeach
        </select>
        @error('category') <p class="form-error">{{ $message }}</p> @enderror
    </div>

    <div class="form-group">
        <label class="form-label" for="department">Department</label>
        <input type="text" id="department" name="department"
               value="{{ old('department', $staff->department ?? '') }}"
               class="form-control">
    </div>

    <div class="form-group">
        <label class="form-label" for="job_title">Job title</label>
        <input type="text" id="job_title" name="job_title"
               value="{{ old('job_title', $staff->job_title ?? '') }}"
               class="form-control">
    </div>
</div>

<div class="form-row">
    <div class="form-group">
        <label class="form-label" for="employment_type">Employment type <span class="required">*</span></label>
        <select id="employment_type" name="employment_type"
                class="form-control @error('employment_type') is-invalid @enderror" required>
            @foreach (['full_time' => 'Full-time', 'part_time' => 'Part-time', 'contract' => 'Contract', 'volunteer' => 'Volunteer', 'intern' => 'Intern'] as $v => $l)
                <option value="{{ $v }}" @selected(old('employment_type', $staff->employment_type ?? 'full_time') === $v)>{{ $l }}</option>
            @endforeach
        </select>
        @error('employment_type') <p class="form-error">{{ $message }}</p> @enderror
    </div>

    <div class="form-group">
        <label class="form-label" for="employment_start_date">Start date <span class="required">*</span></label>
        <input type="date" id="employment_start_date" name="employment_start_date"
               value="{{ old('employment_start_date', $staff->employment_start_date?->toDateString() ?? now()->toDateString()) }}"
               class="form-control @error('employment_start_date') is-invalid @enderror" required>
        @error('employment_start_date') <p class="form-error">{{ $message }}</p> @enderror
    </div>

    <div class="form-group">
        <label class="form-label" for="employment_end_date">End date</label>
        <input type="date" id="employment_end_date" name="employment_end_date"
               value="{{ old('employment_end_date', $staff->employment_end_date?->toDateString() ?? '') }}"
               class="form-control">
    </div>
</div>

<div class="form-row">
    <div class="form-group">
        <label class="form-label" for="status">Status <span class="required">*</span></label>
        <select id="status" name="status" class="form-control" required>
            @foreach (['active' => 'Active', 'on_leave' => 'On Leave', 'suspended' => 'Suspended', 'terminated' => 'Terminated'] as $v => $l)
                <option value="{{ $v }}" @selected(old('status', $staff->status ?? 'active') === $v)>{{ $l }}</option>
            @endforeach
        </select>
    </div>

    <div class="form-group">
        <label class="form-label" for="specialization">Specialization</label>
        <input type="text" id="specialization" name="specialization"
               value="{{ old('specialization', $staff->specialization ?? '') }}"
               class="form-control">
    </div>
</div>

<div class="form-group">
    <label class="form-label" for="professional_qualifications">Professional qualifications</label>
    <textarea id="professional_qualifications" name="professional_qualifications"
              class="form-control" rows="3">{{ old('professional_qualifications', $staff->professional_qualifications ?? '') }}</textarea>
</div>

{{-- ─── Sensitive ────────────────────────────────────── --}}
@can('staff.view_sensitive')
    <div class="form-row">
        <div class="form-group">
            <label class="form-label" for="salary">Salary (monthly)</label>
            <input type="number" step="0.01" id="salary" name="salary"
                   value="{{ old('salary', $staff->salary ?? '') }}"
                   class="form-control">
            <p class="form-help">Visible only to users with sensitive-data access.</p>
        </div>
    </div>
@endcan

<div class="form-group">
    <label class="form-label" for="notes">Internal notes</label>
    <textarea id="notes" name="notes" class="form-control" rows="3">{{ old('notes', $staff->notes ?? '') }}</textarea>
</div>

{{-- ─── Linked Accounts ──────────────────────────────── --}}
<div class="form-group">
    <label class="form-label">Linked login accounts</label>
    <p class="form-help">A staff member may have zero, one, or several login accounts.</p>

    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 6px;
                max-height: 240px; overflow-y: auto; padding: 12px;
                border: 1px solid var(--border-color); border-radius: var(--radius-sm);">
        @forelse ($users as $user)
            <label class="form-check">
                <input type="checkbox" name="user_ids[]" value="{{ $user->id }}"
                       @checked(in_array($user->id, $selectedUserIds))>
                <span>{{ $user->name }} <span style="color: var(--text-muted); font-size: 11px;">({{ $user->email }})</span></span>
            </label>
        @empty
            <p style="color: var(--text-muted); font-size: 13px; grid-column: 1 / -1;">
                No active user accounts available.
            </p>
        @endforelse
    </div>
</div>