<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEmergencyContactRequest;
use App\Http\Requests\UpdateEmergencyContactRequest;
use App\Models\Child;
use App\Models\EmergencyContact;
use Illuminate\Http\RedirectResponse;

class EmergencyContactController extends Controller
{
    public function store(StoreEmergencyContactRequest $request, Child $child): RedirectResponse
    {
        $child->emergencyContacts()->create($request->validated());

        return redirect()
            ->route('admin.children.show', $child)
            ->with('status', 'Emergency contact added.');
    }

    public function update(UpdateEmergencyContactRequest $request, Child $child, EmergencyContact $contact): RedirectResponse
    {
        // Guard against IDOR — verify the contact belongs to this child.
        abort_unless($contact->child_id === $child->id, 404);

        $contact->update($request->validated());

        return redirect()
            ->route('admin.children.show', $child)
            ->with('status', 'Emergency contact updated.');
    }

    public function destroy(Child $child, EmergencyContact $contact): RedirectResponse
    {
        abort_unless($contact->child_id === $child->id, 404);

        $contact->delete();

        return redirect()
            ->route('admin.children.show', $child)
            ->with('status', 'Emergency contact removed.');
    }
}