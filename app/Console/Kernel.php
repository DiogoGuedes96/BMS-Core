<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        \Modules\Calls\Commands\AteriskListenerCommand::class,
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();
        $schedule->command('auth:clear-resets')->everyFifteenMinutes();
    }

    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        $modulesPath = app_path('Modules');

        $modulesConfig = config('modules');
        foreach ($modulesConfig as $moduleName => $isEnabled) {
            if ($isEnabled) {
                $this->load($modulesPath . '/' . ucfirst($moduleName) . '/Commands');
            }
        }

        require base_path('routes/console.php');
    }
}
