<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // Example scheduled tasks for clinic management
        
        // Clean up old queue tickets daily at midnight
        $schedule->command('queue:cleanup')->daily();
        
        // Send appointment reminders every hour during business hours
        $schedule->command('appointments:send-reminders')
                ->hourly()
                ->between('8:00', '17:00')
                ->weekdays();
        
        // Generate daily reports at end of business day
        $schedule->command('reports:daily')->dailyAt('18:00');
        
        // Backup database daily at 2 AM
        $schedule->command('backup:run --only-db')->dailyAt('02:00');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
