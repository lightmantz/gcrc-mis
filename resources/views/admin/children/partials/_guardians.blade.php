@php
    $childGuardians = $child->guardians()
        ->orderByPivot('is_primary', 'desc')
        ->get();

    $allGuardians = \App\Models\Guardian::orderBy('last_name')
        ->orderBy('first_name')
        ->get(['id', 'first_name', 'middle_name', 'last_name', 'phone']);
@endphp

<x-gentelella::card title="Guardians" style="margin-top: 16px;">
    @if ($childGuardians->isEmpty())
        <p style="color: var(--text-muted); font-size: 13px; margin-bottom: 16px;">
            No guardians linked to this child yet.
        </p>
    @else
        <div style="display: flex; flex-direction: column; gap: 12px; margin-bottom: 20px;">
            @foreach ($childGuardians as $guardian)
                <div style="border: 1px solid var(--border-color); border-radius: var(--radius); padding: 12px;">
                    <div style="display: flex; justify-content: space-between; align-items: start; gap: 12px;">
                        <div style="min-width: 0;">
                            <a href="{{ route('admin.guardians.show', $guardian) }}"
                               class="cell-strong" style="font-size: 14px;">
                                {{ $guardian->full_name }}
                            </a>

                            <div style="font-size: 12px; color: var(--text-muted); margin-top: 2px;">
                                {{ ucfirst($guardian->pivot->relationship) }}
                                @if ($guardian->pivot->relationship === 'other' && $guardian->pivot->relationship_other)
                                    — {{ $guardian->pivot->relationship_other }}
                                @endif
                                — {{ $guardian->phone }}
                            </div>

                            <div style="display: flex; flex-wrap: wrap; gap: 4px; margin-top: 8px;">
                                @if ($guardian->pivot->is_primary)
                                    <x-gentelella::badge tone="teal">Primary</x-gentelella::badge>
                                @endif
                                @if ($guardian->pivot->is_legal)
                                    <x-gentelella::badge tone="blue">Legal guardian</x-gentelella::badge>
                                @endif
                                @if ($guardian->pivot->consent_medical)
                                    <x-gentelella::badge tone="green">Medical consent</x-gentelella::badge>
                                @endif
                                @if ($guardian->pivot->consent_education)
                                    <x-gentelella::badge tone="green">Education consent</x-gentelella::badge>
                                @endif
                                @if ($guardian->pivot->consent_photography)
                                    <x-gentelella::badge tone="green">Photo consent</x-gentelella::badge>
                                @endif
                                @if ($guardian->pivot->lives_with_child)
                                    <x-gentelella::badge tone="yellow">Lives with child</x-gentelella::badge>
                                @endif
                            </div>
                        </div>

                        @can('children.edit')
                            <div style="display: flex; gap: 4px; flex-shrink: 0;">
                                <details style="display: inline-block; position: relative;">
                                    <summary class="btn btn-sm btn-outline" style="cursor: pointer; list-style: none;">
                                        Edit
                                    </summary>

                                    <div style="position: absolute; z-index: 20; right: 0; top: 36px; background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius); box-shadow: 0 8px 24px rgba(15, 23, 42, 0.12); padding: 16px; width: 400px;">
                                        <form method="POST"
                                              action="{{ route('admin.children.guardians.update', [$child, $guardian]) }}">
                                            @csrf
                                            @method('PUT')

                                            <div class="form-group">
                                                <label class="form-label">Relationship</label>
                                                <select name="relationship" class="form-control">
                                                    @foreach (['mother','father','grandmother','grandfather','aunt','uncle','sibling','legal_guardian','foster_parent','other'] as $r)
                                                        <option value="{{ $r }}" @selected($guardian->pivot->relationship === $r)>
                                                            {{ ucfirst(str_replace('_', ' ', $r)) }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="form-group">
                                                <label class="form-label">Specify (if "Other")</label>
                                                <input type="text" name="relationship_other" class="form-control"
                                                       value="{{ $guardian->pivot->relationship_other }}">
                                            </div>

                                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 6px; margin: 12px 0;">
                                                @foreach ([
                                                    'is_primary' => 'Primary contact',
                                                    'is_legal' => 'Legal guardian',
                                                    'consent_medical' => 'Medical consent',
                                                    'consent_education' => 'Education consent',
                                                    'consent_photography' => 'Photo consent',
                                                    'lives_with_child' => 'Lives with child',
                                                ] as $field => $label)
                                                    <label class="form-check">
                                                        <input type="checkbox" name="{{ $field }}" value="1"
                                                               @checked($guardian->pivot->$field)>
                                                        <span style="font-size: 12px;">{{ $label }}</span>
                                                    </label>
                                                @endforeach
                                            </div>

                                            <div class="form-group">
                                                <label class="form-label">Notes</label>
                                                <textarea name="notes" class="form-control" rows="2">{{ $guardian->pivot->notes }}</textarea>
                                            </div>

                                            <button type="submit" class="btn btn-primary btn-sm">Save</button>
                                        </form>
                                    </div>
                                </details>

                                <form method="POST"
                                      action="{{ route('admin.children.guardians.destroy', [$child, $guardian]) }}"
                                      style="display: inline;"
                                      onsubmit="return confirm('Unlink {{ $guardian->full_name }} from this child?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Unlink</button>
                                </form>
                            </div>
                        @endcan
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    @can('children.edit')
        <details>
            <summary class="btn btn-outline" style="cursor: pointer; list-style: none;">
                Add Guardian
            </summary>

            <div style="margin-top: 12px; padding: 16px; border: 1px solid var(--border-color); border-radius: var(--radius);">
                <form method="POST" action="{{ route('admin.children.guardians.store', $child) }}">
                    @csrf

                    <div class="form-group">
                        <label class="form-label">Attach an existing guardian</label>
                        <select name="guardian_id" class="form-control">
                            <option value="">— Create a new guardian below —</option>
                            @foreach ($allGuardians as $g)
                                <option value="{{ $g->id }}">
                                    {{ $g->full_name }} ({{ $g->phone }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="divider-label">or create a new one</div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">First name</label>
                            <input type="text" name="first_name" class="form-control">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Middle name</label>
                            <input type="text" name="middle_name" class="form-control">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Last name</label>
                            <input type="text" name="last_name" class="form-control">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone" class="form-control">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Occupation</label>
                            <input type="text" name="occupation" class="form-control">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Address</label>
                        <input type="text" name="address" class="form-control">
                    </div>

                    <hr class="divider-plain">

                    <h4 style="font-size: 12px; text-transform: uppercase; letter-spacing: 0.4px; color: var(--text-muted); margin-bottom: 12px;">
                        Relationship to {{ $child->first_name }}
                    </h4>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Relationship <span class="required">*</span></label>
                            <select name="relationship" class="form-control" required>
                                @foreach (['mother','father','grandmother','grandfather','aunt','uncle','sibling','legal_guardian','foster_parent','other'] as $r)
                                    <option value="{{ $r }}">{{ ucfirst(str_replace('_', ' ', $r)) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Specify (if "Other")</label>
                            <input type="text" name="relationship_other" class="form-control">
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 6px; margin: 12px 0;">
                        @foreach ([
                            'is_primary' => 'Primary contact',
                            'is_legal' => 'Legal guardian',
                            'consent_medical' => 'Medical consent',
                            'consent_education' => 'Education consent',
                            'consent_photography' => 'Photo consent',
                            'lives_with_child' => 'Lives with child',
                        ] as $field => $label)
                            <label class="form-check">
                                <input type="checkbox" name="{{ $field }}" value="1">
                                <span style="font-size: 12px;">{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>

                    <button type="submit" class="btn btn-primary">Add Guardian</button>
                </form>
            </div>
        </details>
    @endcan
</x-gentelella::card>