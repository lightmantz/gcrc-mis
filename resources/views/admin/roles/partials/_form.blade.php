@php
    $editing = isset($role);
    $lockedName = $editing && in_array($role->name, [
        'System Administrator','Center Manager/Director','Clinical/Medical Staff',
        'Physiotherapist','Vocational Training Expert','Occupational Therapist',
        'Speech/Language Therapist','Nurse','Teacher',
        'Head Teacher / Education Coordinator','Special Education Teacher',
        'Teaching Assistant','School Records Officer','School Administrator',
        'Examination/Assessment Officer','Social Worker','Psychologist/Counselor',
        'Matron/Patron','Reception/Records Officer','Pharmacy/Store Officer',
        'Finance/Accounts Officer','Data/Reporting Officer',
    ], true);
@endphp

<div class="form-group">
    <label class="form-label" for="name">
        Role name <span class="required">*</span>
    </label>
    <input type="text" id="name" name="name"
           value="{{ old('name', $role->name ?? '') }}"
           class="form-control @error('name') is-invalid @enderror"
           {{ $lockedName ? 'readonly' : 'required' }}>
    @error('name') <p class="form-error">{{ $message }}</p> @enderror
    @if ($lockedName)
        <p class="form-help">Seeded role names cannot be changed.</p>
    @endif
</div>

@if (! $editing)
    <div class="form-group">
        <label class="form-label" for="clone_from">Clone permissions from</label>
        <select id="clone_from" name="clone_from" class="form-control"
                onchange="window.location.href = '{{ route('admin.roles.create') }}?clone_from=' + encodeURIComponent(this.value)">
            <option value="">Start with no permissions</option>
            @foreach ($clonableRoles as $clonable)
                <option value="{{ $clonable }}" @selected(old('clone_from', $cloneFrom ?? '') === $clonable)>
                    {{ $clonable }}
                </option>
            @endforeach
        </select>
        <p class="form-help">Selecting a role pre-populates the permission checkboxes below.</p>
    </div>
@endif

<div class="form-group">
    <label class="form-label">Permissions</label>

    <div style="display: flex; gap: 8px; align-items: center; margin-bottom: 12px;">
        <input type="text" id="perm-search" class="form-control"
               placeholder="Search permissions…" style="max-width: 320px;">
        <label class="form-check">
            <input type="checkbox" id="select-all-top">
            <span>Select all</span>
        </label>
    </div>

    @error('permissions') <p class="form-error">{{ $message }}</p> @enderror

    <div style="border: 1px solid var(--border-color); border-radius: var(--radius-sm); max-height: 600px; overflow-y: auto;">
        @foreach ($groupedPermissions as $module => $group)
            <details class="accordion-item" style="border: none; border-bottom: 1px solid var(--border-color-light);"
                     {{ $group['granted_count'] > 0 ? 'open' : '' }}>
                <summary class="accordion-summary" style="cursor: pointer; padding: 10px 14px;">
                    <strong>{{ $group['label'] }}</strong>
                    <span style="color: var(--text-muted); font-size: 12px; margin-left: auto;">
                        <span class="group-count" data-module="{{ $module }}">{{ $group['granted_count'] }}</span>
                        / {{ $group['total_count'] }}
                    </span>
                </summary>

                <div class="accordion-content" style="padding: 8px 14px 14px; display: grid; grid-template-columns: repeat(3, 1fr); gap: 4px;">
                    <label class="form-check" style="grid-column: 1 / -1; border-bottom: 1px solid var(--border-color-light); padding-bottom: 4px; margin-bottom: 4px;">
                        <input type="checkbox" class="select-group" data-module="{{ $module }}"
                               {{ $group['granted_count'] === $group['total_count'] ? 'checked' : '' }}>
                        <span style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.4px; color: var(--text-muted);">
                            Select all in this group
                        </span>
                    </label>

                    @foreach ($group['permissions'] as $perm)
                        <label class="form-check">
                            <input type="checkbox"
                                   name="permissions[]"
                                   value="{{ $perm['name'] }}"
                                   data-module="{{ $module }}"
                                   @checked(old("permissions.{$loop->parent->index}.{$loop->index}", $perm['granted']))>
                            <span>{{ $perm['action'] }}</span>
                        </label>
                    @endforeach
                </div>
            </details>
        @endforeach
    </div>
</div>

<script>
(function () {
    const search = document.getElementById('perm-search');
    const selectAll = document.getElementById('select-all-top');
    const groupCheckboxes = document.querySelectorAll('.select-group');
    const permCheckboxes = document.querySelectorAll('input[name="permissions[]"]');

    // Live search
    search?.addEventListener('input', (e) => {
        const q = e.target.value.toLowerCase();
        document.querySelectorAll('.accordion-item').forEach((item) => {
            let visibleCount = 0;
            item.querySelectorAll('label.form-check').forEach((lbl) => {
                if (lbl.querySelector('.select-group')) return; // skip group toggle
                const cb = lbl.querySelector('input[name="permissions[]"]');
                if (!cb) return;
                const match = cb.value.toLowerCase().includes(q);
                lbl.style.display = match ? '' : 'none';
                if (match) visibleCount++;
            });
            item.style.display = visibleCount > 0 ? '' : 'none';
        });
    });

    // Per-group select-all
    groupCheckboxes.forEach((gc) => {
        gc.addEventListener('change', (e) => {
            const module = e.target.dataset.module;
            document.querySelectorAll(`input[name="permissions[]"][data-module="${module}"]`)
                .forEach((cb) => { cb.checked = e.target.checked; });
            updateCount(module);
        });
    });

    // Master select-all
    selectAll?.addEventListener('change', (e) => {
        permCheckboxes.forEach((cb) => { cb.checked = e.target.checked; });
        groupCheckboxes.forEach((gc) => { gc.checked = e.target.checked; });
        document.querySelectorAll('.group-count').forEach((el) => {
            updateCount(el.dataset.module);
        });
    });

    // Live counts
    permCheckboxes.forEach((cb) => {
        cb.addEventListener('change', () => updateCount(cb.dataset.module));
    });

    function updateCount(module) {
        const checked = document.querySelectorAll(`input[name="permissions[]"][data-module="${module}"]:checked`).length;
        const el = document.querySelector(`.group-count[data-module="${module}"]`);
        if (el) el.textContent = checked;
    }
})();
</script>