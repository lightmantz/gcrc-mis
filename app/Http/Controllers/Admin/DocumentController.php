<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDocumentRequest;
use App\Models\Child;
use App\Models\Document;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentController extends Controller
{
    public function store(StoreDocumentRequest $request, Child $child): RedirectResponse
    {
        $file = $request->file('file');
        $disk = config('filesystems.documents_disk', 'local');

        // Store under documents/children/{child_id}/YYYY/MM/
        $directory = "documents/children/{$child->id}/" . now()->format('Y/m');
        $path = $file->store($directory, $disk);

        $child->documents()->create([
            'original_name' => $file->getClientOriginalName(),
            'stored_path'   => $path,
            'mime_type'     => $file->getClientMimeType(),
            'size_bytes'    => $file->getSize(),
            'title'         => $request->input('title') ?: $file->getClientOriginalName(),
            'category'      => $request->input('category'),
            'description'   => $request->input('description'),
            'uploaded_by'   => $request->user()->id,
        ]);

        return redirect()
            ->route('admin.children.show', $child)
            ->with('status', 'Document uploaded.');
    }

    public function download(Document $document): StreamedResponse
    {
        $disk = config('filesystems.documents_disk', 'local');

        abort_unless(Storage::disk($disk)->exists($document->stored_path), 404);

        return Storage::disk($disk)->download(
            $document->stored_path,
            $document->original_name
        );
    }

    public function destroy(Request $request, Document $document): RedirectResponse
    {
        $this->authorize('delete', $document);

        $child = $document->documentable;

        $disk = config('filesystems.documents_disk', 'local');
        Storage::disk($disk)->delete($document->stored_path);

        $document->delete();

        return redirect()
            ->route('admin.children.show', $child)
            ->with('status', 'Document deleted.');
    }
}