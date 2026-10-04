@php
    $user = $user ?? new \App\Models\User();
    $editing = $user->exists;
    $selectedRoles = old('roles', $editing ? $user->roles->pluck('name')->toArray() : []);
@endphp

<div class="form-row">
    <div class="form-group">
        <label class="form-label" for="name">
            Full Name <span class="required">*</span>
        </label>
        <input type="text" id="name" name="name"
               value="{{ old('name', $user->name ?? '') }}"
               class="form-control @error('name') is-invalid @enderror"
               required>
        @error('name') <p class="form-error">{{ $message }}</p> @enderror
    </div>

    <div class="form-group">
        <label class="form-label" for="email">
            Email <span class="required">*</span>
        </label>
        <input type="email" id="email" name="email"
               value="{{ old('email', $user->email ?? '') }}"
               class="form-control @error('email') is-invalid @enderror"
               required>
        @error('email') <p class="form-error">{{ $message }}</p> @enderror
    </div>
</div>

<div class="form-row">
    <div class="form-group">
        <label class="form-label" for="password">
            Password
            @if ($editing)
                <span style="color: var(--text-muted); font-weight: 400;">(leave blank to keep current)</span>
            @else
                <span class="required">*</span>
            @endif
        </label>
        <input type="password" id="password" name="password"
               class="form-control @error('password') is-invalid @enderror"
               autocomplete="new-password"
               {{ $editing ? '' : 'required' }}>
        @error('password') <p class="form-error">{{ $message }}</p> @enderror
    </div>

    <div class="form-group">
        <label class="form-label" for="password_confirmation">Confirm Password</label>
        <input type="password" id="password_confirmation" name="password_confirmation"
               class="form-control" autocomplete="new-password"
               {{ $editing ? '' : 'required' }}>
    </div>
</div>

<div class="form-group">
    <label class="form-label">
        Roles <span class="required">*</span>
    </label>
    @error('roles') <p class="form-error">{{ $message }}</p> @enderror
    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 6px;
                max-height: 240px; overflow-y: auto; padding: 12px;
                border: 1px solid var(--border-color); border-radius: var(--radius-sm);">
        @foreach ($roles as $role)
            <label class="form-check">
                <input type="checkbox" name="roles[]" value="{{ $role }}"
                       @checked(in_array($role, $selectedRoles))>
                <span>{{ $role }}</span>
            </label>
        @endforeach
    </div>
</div>

<div class="form-group">
    <label class="form-check">
        <input type="checkbox" name="is_active" value="1"
               @checked(old('is_active', $user->is_active ?? true))>
        <span>Account is active</span>
    </label>
</div>