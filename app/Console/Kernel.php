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
        Commands\DepriciationCron::class,
        Commands\getPaidInstallment::class,
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
         $schedule->command('depriciation:cron')
             ->everyMinute()
             -> appendOutputTo (storage_path().'/logs/laravel_output.log');
        $schedule->command('getPaidInstallment:cron')
            ->everyMinute()
            -> appendOutputTo (storage_path().'/logs/laravel_output.log');
        $schedule->command('getLoanList:cron')
            ->everyMinute()
            -> appendOutputTo (storage_path().'/logs/laravel_output.log');
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
