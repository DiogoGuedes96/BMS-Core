<?php

namespace App\Console\Commands;

use App\Providers\ModuleServiceProvider;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;

class CreateModule extends Command
{
    protected $signature = 'module:create {moduleName : The name of the new module}';
    protected $description = 'Create a new module structure with controllers, models, routes, and api.php file.';

    public function handle()
    {
        $moduleName = $this->argument('moduleName');
        $modulePath = app_path('Modules/' . ucfirst($moduleName));

        if (File::exists($modulePath)) {
            $this->error("Module '$moduleName' already exists.");
            return;
        }

        File::makeDirectory($modulePath, 0755, true);

        $subDirectories = ['Controllers', 'Models', 'Routes', 'Services'];
        foreach ($subDirectories as $subDir) {
            File::makeDirectory($modulePath . '/' . $subDir, 0755, true);
        }

        $apiRoutesFile = $modulePath . '/Routes/api.php';
        file_put_contents($apiRoutesFile, '<?php' . PHP_EOL);
        file_put_contents($apiRoutesFile, 'use Illuminate\Http\Request;' . PHP_EOL, FILE_APPEND);
        file_put_contents($apiRoutesFile, 'use Illuminate\Support\Facades\Route;' . PHP_EOL, FILE_APPEND);
        file_put_contents($apiRoutesFile, 'use App\Modules\\' . ucfirst($moduleName) . '\Controllers\\'.ucfirst($moduleName).'Controller;' . PHP_EOL, FILE_APPEND);
        file_put_contents($apiRoutesFile, PHP_EOL, FILE_APPEND);

        file_put_contents($apiRoutesFile, 'Route::prefix(\'v1\')->group(function () {' . PHP_EOL, FILE_APPEND);
        file_put_contents($apiRoutesFile, '    Route::get(\'example\', ['.ucfirst($moduleName).'Controller::class, \'example\']);' . PHP_EOL, FILE_APPEND);
        file_put_contents($apiRoutesFile, '});' . PHP_EOL, FILE_APPEND);

        $controllerPath = $modulePath . '/Controllers/' . ucfirst($moduleName) . 'Controller.php';
        $controllerTemplate = file_get_contents(base_path('stubs/Controller.stub'));
        $controllerTemplate = str_replace('{{ModuleName}}', ucfirst($moduleName), $controllerTemplate);
        file_put_contents($controllerPath, $controllerTemplate);

        $servicePath = $modulePath . '/Services/' . ucfirst($moduleName) . 'Service.php';
        $serviceTemplate = file_get_contents(base_path('stubs/Service.stub'));
        $serviceTemplate = str_replace('{{ModuleName}}', ucfirst($moduleName), $serviceTemplate);
        file_put_contents($servicePath, $serviceTemplate);

        $rootApiRoutesFile = base_path('routes/api.php');
        $newModuleRoute = "\nRoute::prefix('".strtolower($moduleName)."')->group(function () {" . PHP_EOL;
        $newModuleRoute .= "\nif (config('modules." . strtolower($moduleName) . "')) {" . PHP_EOL;
        $newModuleRoute .= "    include base_path('app/Modules/" . ucfirst($moduleName) . "/Routes/api.php');" . PHP_EOL;
        $newModuleRoute .= "}" . PHP_EOL;
        $newModuleRoute .= "});" . PHP_EOL;

        file_put_contents($rootApiRoutesFile, $newModuleRoute, FILE_APPEND);

        $modules = Config::get('modules', []);
        $modules[$moduleName] = true;
        Config::set('modules', $modules);
        $this->writeConfig();

        $this->info("Module '$moduleName' created successfully.");
        $this->info("Module structure and routes/api.php file generated.");
        $this->info("Module route added to the root project's routes/api.php file.");
    }

    private function writeConfig()
    {
        $configPath = config_path('modules.php');
        $configContent = '<?php return ' . var_export(Config::get('modules', []), true) . ';';
        file_put_contents($configPath, $configContent);
    }
}
