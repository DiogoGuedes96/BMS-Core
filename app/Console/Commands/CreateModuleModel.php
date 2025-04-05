<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Filesystem\Filesystem;

class CreateModuleModel extends Command
{
    protected $signature = 'module:make-model {moduleName} {modelName}';
    protected $description = 'Create a model for a module';

    public function handle()
    {
        $moduleName = Str::studly($this->argument('moduleName'));
        $modelName = Str::studly($this->argument('modelName'));

        $modelContent = $this->getModelContent($moduleName, $modelName);

        $filesystem = new Filesystem();
        $modelPath = "app/Modules/{$moduleName}/Models/{$modelName}.php";
        $filesystem->put($modelPath, $modelContent);

        $this->info("Model '{$modelName}' for the module '{$moduleName}' created successfully.");
    }

    protected function getModelContent($moduleName, $modelName)
    {
        $stubPath = base_path('stubs/Model.stub');
        $modelStub = file_get_contents($stubPath);

        $modelStub = str_replace('{{ModuleName}}', $moduleName, $modelStub);
        $modelStub = str_replace('{{ModelName}}', $modelName, $modelStub);

        return $modelStub;
    }
}
