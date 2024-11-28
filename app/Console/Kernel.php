<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\SendCertificateExpiryNotifications::class,
        Commands\GenerateSitemap::class,
        Commands\SendUncompletedCourseReminders::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // Check for expiring certificates daily at midnight
        $schedule->command('certificates:check-expiry')->dailyAt('00:00');
        
        // Generate sitemap daily at 1 AM
        $schedule->command('sitemap:generate')->dailyAt('01:00');

        // Send uncompleted course reminders every Monday at 9 AM
        $schedule->command('reminders:uncompleted-courses')
            ->weekly()
            ->mondays()
            ->at('09:00')
            ->timezone('Europe/London');
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
