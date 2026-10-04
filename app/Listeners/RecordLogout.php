<?php

namespace App\Listeners;

use App\Models\LoginHistory;
use App\Models\User;
use Illuminate\Auth\Events\Logout;

class RecordLogout
{
    public function handle(Logout $event): void
    {
        $user = $event->user;

        if (! $user instanceof User) {
            return;
        }

        // Find the most recent open session (logged in, not yet out).
        $lastOpen = LoginHistory::where('user_id', $user->id)
            ->where('successful', true)
            ->whereNull('logged_out_at')
            ->latest('logged_in_at')
            ->first();

        if ($lastOpen) {
            $lastOpen->update(['logged_out_at' => now()]);
        }
    }
}