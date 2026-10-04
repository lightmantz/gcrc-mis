<script id="assessment-types-data" type="application/json">
{!! json_encode(collect($types)->map(fn ($t) => [
    'key' => $t::key(),
    'label' => $t->label(),
    'description' => $t->description(),
    'assessor_category' => $t->assessorCategory(),
    'sections' => $t->sections(),
])->values(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
</script>

<script>
(function () {
    const dataEl = document.getElementById('assessment-types-data');
    if (!dataEl) return;

    const types = JSON.parse(dataEl.textContent || '[]');
    const typeSelect = document.getElementById('type');
    const fieldsContainer = document.getElementById('assessment-fields');
    const description = document.getElementById('type-description');
    const assessorSelect = document.getElementById('assessor_id');

    if (!typeSelect || !fieldsContainer) return;

    function currentFindings() {
        // Preserve already-entered values when swapping types.
        const data = {};
        fieldsContainer.querySelectorAll('input, select, textarea').forEach((el) => {
            const match = el.name.match(/^findings\[(.+)\]$/);
            if (match) data[match[1]] = el.value;
        });
        return data;
    }

    function renderFields(typeKey, existing) {
        const type = types.find((t) => t.key === typeKey);
        if (!type) {
            fieldsContainer.innerHTML = '<p style="margin-top: 24px; padding: 24px; text-align: center; color: var(--text-muted); border: 1px dashed var(--border-color); border-radius: var(--radius);">Select an assessment type above to load its fields.</p>';
            if (description) description.textContent = '';
            return;
        }

        if (description) description.textContent = type.description || '';

        const html = [];
        for (const [sectionLabel, fields] of Object.entries(type.sections)) {
            html.push(`<div class="assessment-section" style="margin-top: 24px;">`);
            html.push(`<h3 style="font-size: 12px; text-transform: uppercase; letter-spacing: 0.4px; color: var(--text-muted); margin-bottom: 12px;">${escapeHtml(sectionLabel)}</h3>`);
            html.push(`<div class="form-row">`);

            for (const [name, def] of Object.entries(fields)) {
                const value = existing[name] ?? '';
                const label = def.label || name;
                const required = !!def.required;
                const inputType = def.type || 'text';
                const isFullWidth = inputType === 'textarea';

                html.push(`<div class="form-group" style="${isFullWidth ? 'grid-column: 1 / -1;' : ''}">`);
                html.push(`<label class="form-label" for="finding_${name}">${escapeHtml(label)}${required ? ' <span class="required">*</span>' : ''}</label>`);

                if (inputType === 'textarea') {
                    html.push(`<textarea id="finding_${name}" name="findings[${name}]" class="form-control" rows="3">${escapeHtml(value)}</textarea>`);
                } else if (inputType === 'select') {
                    html.push(`<select id="finding_${name}" name="findings[${name}]" class="form-control" ${required ? 'required' : ''}>`);
                    html.push(`<option value="">—</option>`);
                    for (const [optVal, optLabel] of Object.entries(def.options || {})) {
                        const selected = String(value) === String(optVal) ? 'selected' : '';
                        html.push(`<option value="${escapeAttr(optVal)}" ${selected}>${escapeHtml(optLabel)}</option>`);
                    }
                    html.push(`</select>`);
                } else if (inputType === 'number') {
                    html.push(`<input type="number" step="any" id="finding_${name}" name="findings[${name}]" value="${escapeAttr(value)}" class="form-control" ${required ? 'required' : ''}>`);
                } else {
                    html.push(`<input type="text" id="finding_${name}" name="findings[${name}]" value="${escapeAttr(value)}" class="form-control" ${required ? 'required' : ''}>`);
                }

                if (def.help) html.push(`<p class="form-help">${escapeHtml(def.help)}</p>`);
                html.push(`</div>`);
            }

            html.push(`</div></div>`);
        }

        fieldsContainer.innerHTML = html.join('');

        // Filter assessor list by category.
        if (assessorSelect && type.assessor_category) {
            assessorSelect.querySelectorAll('option').forEach((opt) => {
                if (!opt.dataset.category) return;
                opt.hidden = opt.dataset.category !== type.assessor_category;
                if (opt.hidden && opt.selected) {
                    assessorSelect.value = '';
                }
            });
        }
    }

    function escapeHtml(s) {
        return String(s).replace(/[&<>"']/g, (c) => ({
            '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
        })[c]);
    }
    function escapeAttr(s) { return escapeHtml(s); }

    typeSelect.addEventListener('change', () => {
        renderFields(typeSelect.value, currentFindings());
    });

    // On initial load, if a type is already selected (validation failure case),
    // render its fields.
    if (typeSelect.value) {
        renderFields(typeSelect.value, currentFindings());
    }
})();
</script>