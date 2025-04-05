<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class CreateModuleMigration extends Command
{
    protected $signature = 'module:make-migration {moduleName} {migrationName}';
    protected $description = 'Create a migration and seed for a module';

    public function handle()
    {
        $moduleName = Str::studly($this->argument('moduleName'));
        $migrationName = Str::studly($this->argument('migrationName'));

        $this->makeMigration($moduleName, $migrationName);

        $this->info("Created successfully to migration '{$migrationName}' to module '{$moduleName}'.");
    }

    protected function makeMigration($moduleName, $migrationName)
    {
        $migrationName = Str::snake($migrationName);
        $moduleName = ucfirst($moduleName);

        $this->call('make:migration', [
            'name' => $migrationName,
            '--path' => "app/Modules/{$moduleName}/Database/Migrations",
        ]);
    }
}
