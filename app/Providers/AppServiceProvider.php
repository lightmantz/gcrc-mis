<?php

namespace App\Providers;

use App\Listeners\RecordFailedLogin;
use App\Listeners\RecordLogin;
use App\Listeners\RecordLogout;
use App\Models\Child;
use App\Models\Guardian;
use App\Models\Staff;
use App\Models\User;
use App\Observers\ChildObserver;
use App\Observers\StaffObserver;
use App\Policies\ChildPolicy;
use App\Policies\GuardianPolicy;
use App\Policies\RolePolicy;
use App\Policies\StaffPolicy;
use App\Policies\UserPolicy;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Spatie\Permission\Models\Role;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Policies
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(Role::class, RolePolicy::class);
        Gate::policy(Staff::class, StaffPolicy::class);
        Gate::policy(Child::class, ChildPolicy::class);
        Gate::policy(Guardian::class, GuardianPolicy::class);

        // Observers
        Staff::observe(StaffObserver::class);
        Child::observe(ChildObserver::class);

        // Event listeners
        Event::listen(Login::class, RecordLogin::class);
        Event::listen(Failed::class, RecordFailedLogin::class);
        Event::listen(Logout::class, RecordLogout::class);
    }
}