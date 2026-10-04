@php
    $contacts = $child->emergencyContacts()->ordered()->get();
@endphp

<x-gentelella::card title="Emergency Contacts" style="margin-top: 16px;">
    @if ($contacts->isEmpty())
        <p style="color: var(--text-muted); font-size: 13px; margin-bottom: 16px;">
            No emergency contacts yet.
        </p>
    @else
        <div style="display: flex; flex-direction: column; gap: 8px; margin-bottom: 20px;">
            @foreach ($contacts as $contact)
                <div style="border: 1px solid var(--border-color); border-radius: var(--radius); padding: 12px;">
                    <div style="display: flex; justify-content: space-between; align-items: start; gap: 12px;">
                        <div>
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <strong>{{ $contact->name }}</strong>
                                <x-gentelella::badge tone="blue">Priority {{ $contact->priority }}</x-gentelella::badge>
                            </div>
                            <div style="font-size: 12px; color: var(--text-muted); margin-top: 2px;">
                                {{ $contact->relationship ?? '—' }} — {{ $contact->phone }}
                                @if ($contact->alternate_phone)
                                    / {{ $contact->alternate_phone }}
                                @endif
                            </div>
                            @if ($contact->email)
                                <div style="font-size: 12px; color: var(--text-muted);">
                                    {{ $contact->email }}
                                </div>
                            @endif
                        </div>

                        @can('children.edit')
                            <div style="display: flex; gap: 4px;">
                                <details style="display: inline-block;">
                                    <summary class="btn btn-sm btn-outline" style="cursor: pointer; list-style: none;">
                                        Edit
                                    </summary>
                                    <div style="position: absolute; z-index: 20; background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius); box-shadow: 0 8px 24px rgba(15, 23, 42, 0.12); padding: 16px; margin-top: 8px; width: 400px;">
                                        <form method="POST" action="{{ route('admin.children.emergency-contacts.update', [$child, $contact]) }}">
                                            @csrf
                                            @method('PUT')

                                            <div class="form-row">
                                                <div class="form-group">
                                                    <label class="form-label">Name</label>
                                                    <input type="text" name="name" class="form-control" value="{{ $contact->name }}" required>
                                                </div>
                                                <div class="form-group">
                                                    <label class="form-label">Relationship</label>
                                                    <input type="text" name="relationship" class="form-control" value="{{ $contact->relationship }}">
                                                </div>
                                            </div>

                                            <div class="form-row">
                                                <div class="form-group">
                                                    <label class="form-label">Phone</label>
                                                    <input type="text" name="phone" class="form-control" value="{{ $contact->phone }}" required>
                                                </div>
                                                <div class="form-group">
                                                    <label class="form-label">Alternate phone</label>
                                                    <input type="text" name="alternate_phone" class="form-control" value="{{ $contact->alternate_phone }}">
                                                </div>
                                            </div>

                                            <div class="form-row">
                                                <div class="form-group">
                                                    <label class="form-label">Email</label>
                                                    <input type="email" name="email" class="form-control" value="{{ $contact->email }}">
                                                </div>
                                                <div class="form-group">
                                                    <label class="form-label">Priority</label>
                                                    <input type="number" name="priority" class="form-control" value="{{ $contact->priority }}" min="1" max="10" required>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="form-label">Address</label>
                                                <input type="text" name="address" class="form-control" value="{{ $contact->address }}">
                                            </div>

                                            <div class="form-group">
                                                <label class="form-label">Notes</label>
                                                <textarea name="notes" class="form-control" rows="2">{{ $contact->notes }}</textarea>
                                            </div>

                                            <button type="submit" class="btn btn-primary btn-sm">Save</button>
                                        </form>
                                    </div>
                                </details>

                                <form method="POST" action="{{ route('admin.children.emergency-contacts.destroy', [$child, $contact]) }}"
                                      style="display: inline;"
                                      onsubmit="return confirm('Remove this emergency contact?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Remove</button>
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
                Add Emergency Contact
            </summary>

            <div style="margin-top: 12px; padding: 16px; border: 1px solid var(--border-color); border-radius: var(--radius);">
                <form method="POST" action="{{ route('admin.children.emergency-contacts.store', $child) }}">
                    @csrf

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Name <span class="required">*</span></label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Relationship</label>
                            <input type="text" name="relationship" class="form-control"
                                   placeholder="e.g. Neighbor, Aunt, Family friend">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Priority <span class="required">*</span></label>
                            <input type="number" name="priority" class="form-control" value="1" min="1" max="10" required>
                            <p class="form-help">1 = first to call</p>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Phone <span class="required">*</span></label>
                            <input type="text" name="phone" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Alternate phone</label>
                            <input type="text" name="alternate_phone" class="form-control">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Address</label>
                        <input type="text" name="address" class="form-control">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control" rows="2"></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary">Add Contact</button>
                </form>
            </div>
        </details>
    @endcan
</x-gentelella::card>