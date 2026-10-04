{{--
    Documents partial — works with any polymorphic documentable.

    Usage:
        @include('admin._shared._documents', [
            'documentable' => $assessment,   // or $child, $referral, ...
            'title' => 'Attachments',         // optional, defaults to "Documents"
        ])
--}}
@php
    // Accept either `documentable` (new usage) or `child` (legacy).
    $documentable = $documentable ?? $child ?? null;
    $title = $title ?? 'Documents';

    if (! $documentable) {
        return;
    }

    $documents = $documentable->documents()->with('uploadedBy')->get();

    // Route for uploading — the child route still exists; assessments reuse it
    // through the polymorphic upload endpoint added in Module 8c.
    $uploadRoute = null;
    if ($documentable instanceof \App\Models\Child) {
        $uploadRoute = route('admin.children.documents.store', $documentable);
    }
@endphp

<x-gentelella::card :title="$title">
    @if ($documents->isEmpty())
        <p style="color: var(--text-muted); font-size: 13px; margin-bottom: 16px;">
            No documents attached yet.
        </p>
    @else
        <div style="display: flex; flex-direction: column; gap: 8px; margin-bottom: 16px;">
            @foreach ($documents as $document)
                <div style="border: 1px solid var(--border-color); border-radius: var(--radius); padding: 10px 12px;">
                    <div style="display: flex; justify-content: space-between; align-items: start; gap: 12px;">
                        <div style="min-width: 0;">
                            <div style="font-size: 13px; font-weight: 500; word-break: break-all;">
                                {{ $document->title ?: $document->original_name }}
                            </div>
                            <div style="font-size: 11.5px; color: var(--text-muted); margin-top: 2px;">
                                {{ $document->category_label }}
                                — {{ $document->size_for_humans }}
                                @if ($document->uploadedBy)
                                    — uploaded by {{ $document->uploadedBy->name }}
                                @endif
                            </div>
                        </div>

                        <div style="display: flex; gap: 4px; flex-shrink: 0;">
                            <a href="{{ route('admin.documents.download', $document) }}"
                               class="btn btn-sm btn-outline">
                                Download
                            </a>

                            @can('delete', $document)
                                <form method="POST" action="{{ route('admin.documents.destroy', $document) }}"
                                      style="display: inline;"
                                      onsubmit="return confirm('Delete this document?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                </form>
                            @endcan
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    @if ($uploadRoute)
        @can('children.edit')
            <details>
                <summary class="btn btn-outline" style="cursor: pointer; list-style: none;">
                    Upload Document
                </summary>

                <div style="margin-top: 12px; padding: 16px; border: 1px solid var(--border-color); border-radius: var(--radius);">
                    <form method="POST" action="{{ $uploadRoute }}" enctype="multipart/form-data">
                        @csrf

                        <div class="form-group">
                            <label class="form-label">File <span class="required">*</span></label>
                            <input type="file" name="file" class="form-control" required
                                   accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                            <p class="form-help">PDF, JPG, PNG, DOC, or DOCX up to 10 MB.</p>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Category <span class="required">*</span></label>
                                <select name="category" class="form-control" required>
                                    <option value="referral_letter">Referral Letter</option>
                                    <option value="medical_report">Medical Report</option>
                                    <option value="assessment_report">Assessment Report</option>
                                    <option value="consent_form">Consent Form</option>
                                    <option value="discharge_document">Discharge Document</option>
                                    <option value="identification">Identification</option>
                                    <option value="photo">Photo</option>
                                    <option value="other" selected>Other</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Title</label>
                                <input type="text" name="title" class="form-control"
                                       placeholder="Optional display name">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="2"></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary">Upload</button>
                    </form>
                </div>
            </details>
        @endcan
    @else
        <p style="color: var(--text-muted); font-size: 12px;">
            Document uploads for this record type will be enabled in a future update.
        </p>
    @endif
</x-gentelella::card>