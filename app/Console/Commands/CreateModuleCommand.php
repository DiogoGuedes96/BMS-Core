<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\File;

class CreateModuleCommand extends Command
{
    protected $signature = 'module:make-command {module} {commandName}';
    protected $description = 'Create a custom command from modules';

    public function handle()
    {
        $modulesPath = app_path('Modules');
        $module = Str::studly($this->argument('module'));
        $commandName = Str::studly($this->argument('commandName'));
        $commandModulePath = $modulesPath . '/' . ucfirst($module) . '/Commands';

        if (!File::exists($commandModulePath)) {
            File::makeDirectory($commandModulePath, 0755, true, true);
        }

        $commandFile = "$commandModulePath/$commandName.php";

        if (file_exists($commandFile)) {
            $this->error("The command '{$commandName}' already exists.");
            return;
        }

        $commandContent = $this->getCommandContent($commandName);

        $filesystem = new Filesystem();
        $filesystem->put($commandFile, $commandContent);

        $this->info("Command '{$commandName}' created successfully.");
    }

    protected function getCommandContent($commandName)
    {
        $stubPath = base_path('stubs/Command.stub');
        $commandStub = file_get_contents($stubPath);

        $stub = str_replace('{{CommandName}}', $commandName, $commandStub);
        $stub = str_replace('{{Module}}', ucfirst($this->argument('module')), $stub);

        return $stub;
    }
}
