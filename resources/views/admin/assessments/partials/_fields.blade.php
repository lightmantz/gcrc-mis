@php
    $type = $type ?? null;
@endphp

<div id="assessment-fields">
    @if ($type)
        @foreach ($type->sections() as $sectionLabel => $fields)
            <div class="assessment-section" style="margin-top: 24px;">
                <h3 style="font-size: 12px; text-transform: uppercase; letter-spacing: 0.4px; color: var(--text-muted); margin-bottom: 12px;">
                    {{ $sectionLabel }}
                </h3>

                <div class="form-row">
                    @foreach ($fields as $name => $def)
                        @php
                            $value = old("findings.{$name}", $findings[$name] ?? '');
                            $label = $def['label'] ?? ucfirst($name);
                            $inputType = $def['type'] ?? 'text';
                            $required = ! empty($def['required']);
                            $errorKey = "findings.{$name}";
                        @endphp

                        <div class="form-group" style="{{ $inputType === 'textarea' ? 'grid-column: 1 / -1;' : '' }}">
                            <label class="form-label" for="finding_{{ $name }}">
                                {{ $label }}
                                @if ($required) <span class="required">*</span> @endif
                            </label>

                            @switch($inputType)
                                @case('textarea')
                                    <textarea id="finding_{{ $name }}" name="findings[{{ $name }}]"
                                              class="form-control @error($errorKey) is-invalid @enderror"
                                              rows="3">{{ $value }}</textarea>
                                    @break

                                @case('select')
                                    <select id="finding_{{ $name }}" name="findings[{{ $name }}]"
                                            class="form-control @error($errorKey) is-invalid @enderror"
                                            {{ $required ? 'required' : '' }}>
                                        <option value="">—</option>
                                        @foreach ($def['options'] ?? [] as $optValue => $optLabel)
                                            <option value="{{ $optValue }}" @selected($value === $optValue)>
                                                {{ $optLabel }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @break

                                @case('number')
                                    <input type="number" step="any" id="finding_{{ $name }}"
                                           name="findings[{{ $name }}]" value="{{ $value }}"
                                           class="form-control @error($errorKey) is-invalid @enderror"
                                           {{ $required ? 'required' : '' }}>
                                    @break

                                @default
                                    <input type="text" id="finding_{{ $name }}"
                                           name="findings[{{ $name }}]" value="{{ $value }}"
                                           class="form-control @error($errorKey) is-invalid @enderror"
                                           {{ $required ? 'required' : '' }}>
                            @endswitch

                            @if (! empty($def['help']))
                                <p class="form-help">{{ $def['help'] }}</p>
                            @endif

                            @error($errorKey) <p class="form-error">{{ $message }}</p> @enderror
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    @else
        <p style="margin-top: 24px; padding: 24px; text-align: center; color: var(--text-muted); border: 1px dashed var(--border-color); border-radius: var(--radius);">
            Select an assessment type above to load its fields.
        </p>
    @endif
</div>