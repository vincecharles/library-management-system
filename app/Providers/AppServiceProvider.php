<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use App\Models\Permission;
use App\Models\User;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register permission-based Gates dynamically
        $this->registerPermissionGates();
    }

    /**
     * Load all permissions from the database and register a Gate for each.
     */
    protected function registerPermissionGates(): void
    {
        // Guard against table not existing (e.g., during migrations)
        try {
            if (!Schema::hasTable('permissions')) {
                return;
            }

            $permissions = Permission::all();

            foreach ($permissions as $permission) {
                Gate::define($permission->name, function (User $user) use ($permission) {
                    return $user->hasPermission($permission->name);
                });
            }
        } catch (\Exception $e) {
            // Silently fail if DB is not yet available (migration context)
        }
    }
}
