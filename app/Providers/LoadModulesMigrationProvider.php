<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;

class LoadModulesMigrationProvider extends ServiceProvider
{
    public function boot()
    {
        $modulesConfig = config('modules');
        $modulesPath = app_path('Modules');

        if (empty($_SERVER['argv'])) {
            return;
        }

        $command = $_SERVER['argv'];

        if ($this->app->runningInConsole() && isset($command[1]) && str_contains($command[1], 'migrate'))
        {
            foreach ($modulesConfig as $moduleName => $isEnabled) {
                if ($isEnabled) {
                    $module = ucfirst($moduleName);
                    $this->loadModuleMigrations("$modulesPath/$module/Database/Migrations");
                }
            }
        }

        if (
            $this->app->runningInConsole()
                && (isset($command[1]) && str_contains($command[1], 'db:seed'))
        ){
            foreach ($modulesConfig as $moduleName => $isEnabled) {
                if ($isEnabled) {
                    $module = ucfirst($moduleName);
                    $this->loadModuleSeeders($modulesPath, $module);
                }
            }
        }
    }

    private function loadModuleMigrations(string $migrationPath)
    {
        if (file_exists($migrationPath)) {
            $this->loadMigrationsFrom($migrationPath);
        }
    }

    private function loadModuleSeeders(string $modulePath, string $moduleName)
    {
        $seederModulePath = "$modulePath/$moduleName/Database/Seeders";

        if (file_exists($seederModulePath)) {
            $namespace = 'App\\Modules\\' . $moduleName . '\\Database\\Seeders';

            echo "Seeding: $namespace\\DatabaseSeeder\n";

            Artisan::call('db:seed', [
                '--class' => "$namespace\\DatabaseSeeder",
            ]);

            echo "Seeded: $namespace\\DatabaseSeeder (" . Artisan::output() . ")\n";
        }
    }
}
