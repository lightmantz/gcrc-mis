<?php

namespace App\Listeners;

use App\Models\LoginHistory;
use App\Models\User;
use Illuminate\Auth\Events\Login;

class RecordLogin
{
    public function handle(Login $event): void
    {
        $user = $event->user;

        if (! $user instanceof User) {
            return;
        }

        LoginHistory::create([
            'user_id'     => $user->id,
            'email'       => $user->email,
            'ip_address'  => request()->ip() ?? '0.0.0.0',
            'user_agent'  => request()->userAgent(),
            'successful'  => true,
            'logged_in_at' => now(),
        ]);

        // Also update the user record for quick "last login" display.
        $user->forceFill([
            'last_login_at' => now(),
            'last_login_ip' => request()->ip(),
        ])->save();
    }
}