<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CreateModuleInterface extends Command
{
    protected $signature = 'module:make-interface {moduleName : The name of the module} {interfaceName : The name of the interface}';
    protected $description = 'Generate an interface for the specified module';

    public function handle()
    {
        $moduleName = ucfirst($this->argument('moduleName'));
        $interfaceName = $this->argument('interfaceName');

        $modulePath = base_path("app/Modules/$moduleName/Interfaces");

        if (!File::exists($modulePath)) {
            File::makeDirectory($modulePath, 0755, true, true);
        }

        $interfacePath = "$modulePath/$interfaceName.php";

        if (File::exists($interfacePath)) {
            $this->error("The interface $interfaceName already exists in the module $moduleName.");
            return;
        }

        File::put($interfacePath, $this->generateInterfaceContents($interfaceName, $moduleName));

        $this->info("The interface $interfaceName has been created in the module $moduleName.");
    }

    protected function generateInterfaceContents(string $interfaceName, string $moduleName)
    {
        return "<?php\n\nnamespace App\Modules\\$moduleName\\Interfaces;\n\ninterface $interfaceName\n{\n    // Define your interface methods here\n}";
    }
}
