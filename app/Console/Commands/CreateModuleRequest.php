<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CreateModuleRequest extends Command
{
    protected $signature = 'module:make-request {moduleName : The name of the module} {requestName : The name of the request}';
    protected $description = 'Generate a custom request for the specified module';

    public function handle()
    {
        $moduleName = $this->argument('moduleName');
        $requestName = $this->argument('requestName');

        $modulePath = base_path("app/Modules/$moduleName/Requests");

        if (!File::exists($modulePath)) {
            File::makeDirectory($modulePath, 0755, true, true);
        }

        $requestPath = "$modulePath/$requestName.php";

        if (File::exists($requestPath)) {
            $this->error("The request $requestName already exists in the module $moduleName.");
            return;
        }

        File::put($requestPath, $this->generateRequestContents($requestName, $moduleName));

        $this->info("The request $requestName has been created in the module $moduleName.");
    }

    protected function generateRequestContents(string $requestName, string $moduleName)
    {
        return "<?php\n\nnamespace App\Modules\\$moduleName\\Requests;\n\nuse Illuminate\Foundation\Http\FormRequest;\n\nclass $requestName extends FormRequest\n{\n    public function rules()\n    {\n        return [\n            // Define your validation rules here\n        ];\n    }\n}";
    }
}
