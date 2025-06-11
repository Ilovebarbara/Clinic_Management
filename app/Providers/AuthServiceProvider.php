<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // Define gates for role-based access control
        Gate::define('manage-users', function ($user) {
            return $user->role === 'super_admin';
        });

        Gate::define('manage-patients', function ($user) {
            return in_array($user->role, ['super_admin', 'physician', 'nurse', 'staff']);
        });

        Gate::define('manage-appointments', function ($user) {
            return in_array($user->role, ['super_admin', 'physician', 'nurse', 'staff']);
        });

        Gate::define('manage-medical-records', function ($user) {
            return in_array($user->role, ['super_admin', 'physician', 'nurse']);
        });

        Gate::define('manage-queue', function ($user) {
            return in_array($user->role, ['super_admin', 'physician', 'nurse', 'staff']);
        });

        Gate::define('view-reports', function ($user) {
            return in_array($user->role, ['super_admin', 'physician']);
        });

        Gate::define('system-admin', function ($user) {
            return $user->role === 'super_admin';
        });
    }
}
