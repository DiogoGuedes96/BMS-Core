<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ModulesServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        // Load the routes for enabled modules
        foreach (config('modules') as $moduleName => $isEnabled) {
            if ($isEnabled) {
                $routesPath = base_path("app/Modules/{$moduleName}/routes/api.php");
                if (file_exists($routesPath)) {
                    $this->loadRoutesFrom($routesPath);
                }
            }
        }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // You can add any module-specific service registrations here
    }
}
