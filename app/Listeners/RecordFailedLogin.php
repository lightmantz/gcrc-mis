<?php

namespace App\Listeners;

use App\Models\LoginHistory;
use App\Models\User;
use Illuminate\Auth\Events\Failed;

class RecordFailedLogin
{
    public function handle(Failed $event): void
    {
        // $event->user is null if the email didn't match any account.
        // $event->credentials contains the submitted email/username.
        $submitted = $event->credentials['email'] ?? 'unknown';

        LoginHistory::create([
            'user_id'        => $event->user?->id,
            'email'          => $submitted,
            'ip_address'     => request()->ip() ?? '0.0.0.0',
            'user_agent'     => request()->userAgent(),
            'successful'     => false,
            'failure_reason' => $event->user
                ? 'Invalid password'
                : 'Unknown email',
            'logged_in_at'   => now(),
        ]);
    }
}