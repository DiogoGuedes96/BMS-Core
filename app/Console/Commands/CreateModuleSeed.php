<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Filesystem\Filesystem;

class CreateModuleSeed extends Command
{
    protected $signature = 'module:make-seed {moduleName} {seedName}';
    protected $description = 'Create a seed for a module';

    public function handle()
    {
        $moduleName = Str::studly($this->argument('moduleName'));
        $seedName = Str::studly($this->argument('seedName'));

        $this->makeSeed($moduleName, $seedName);

        $this->info("Created successfully to migration '{$seedName}' to module '{$moduleName}'.");
    }

    protected function makeSeed($moduleName, $seedName)
    {
        $seedName = Str::studly($seedName) . 'Seeder';

        // Set the seed class name to the specific module namespace
        $this->call('make:seed', [
            'name' => $seedName,
        ]);

        $modulesPath = app_path('Modules');
        $fileSystem = new Filesystem();
        $seedFile = database_path("seeders/{$seedName}.php");

        if (!file_exists($modulesPath."/{$moduleName}/Database/Seeders")) {
            $fileSystem->makeDirectory($modulesPath."/{$moduleName}/Database/Seeders", 0755, true, true);
        }

        $destinationPath = $modulesPath."/{$moduleName}/Database/Seeders/{$seedName}.php";

        $fileSystem->move($seedFile, $destinationPath);
    }
}
