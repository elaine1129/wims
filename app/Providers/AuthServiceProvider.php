<?php

namespace App\Providers;

use App\Models\CycleCounting;
use App\Policies\CycleCountPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
        CycleCounting::class => CycleCountPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('isAdmin', function (User $user) {
            return $user->role == 'Admin';
        });
        Gate::define('isManager', function (User $user) {
            return $user->role == 'Manager';
        });
        Gate::define('isStaff', function (User $user) {
            return $user->role == 'Staff';
        });
    }
}
