<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;

class ModuleDisable extends Command
{
    protected $signature = 'module:disable {moduleName : The name of the module to disable}';
    protected $description = 'Disable a module in the config/modules.php file';

    public function handle()
    {
        $moduleName = $this->argument('moduleName');

        // Get the existing modules from the configuration
        $modules = Config::get('modules', []);

        // Check if the module exists in the configuration
        if (!array_key_exists($moduleName, $modules)) {
            $this->info("Module '{$moduleName}' does not exist.");
            return;
        }

        // Disable the module by setting its value to false
        $modules[$moduleName] = false;

        // Update the modules configuration with the disabled module
        Config::set('modules', $modules);

        // Save the updated configuration to the file
        $this->writeConfig();

        $this->info("Module '{$moduleName}' disabled successfully.");
    }

    private function writeConfig()
    {
        $configPath = config_path('modules.php');
        $configContent = '<?php return ' . var_export(Config::get('modules', []), true) . ';';
        file_put_contents($configPath, $configContent);
    }
}
