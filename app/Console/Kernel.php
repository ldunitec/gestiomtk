<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{

  protected function schedule(Schedule $schedule)
{
    // $schedule->command('generate:subscriptions')->daily();
    $schedule->command('monitorear:equipos')->everyMinute();
    // $schedule->command('monitorear:equipos')->daily();
}

    /**
     * Define the application's command schedule.
     */
   
    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
