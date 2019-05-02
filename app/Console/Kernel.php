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
        //
        \App\Console\Commands\AutoFollow::class,
        \App\Console\Commands\AutoUnfollow::class,
        \App\Console\Commands\AutoTweet::class,
        \App\Console\Commands\AutoLike::class,
        \App\Console\Commands\InspectActiveUser::class,
        \App\Console\Commands\InspectNotFollowback::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();
        $schedule->command('auto:follow')->cron('0 */2 * * *');
        $schedule->command('auto:unfollow')->hourly();
        $schedule->command('auto:like')->hourlyAt(45);
        $schedule->command('auto:tweet')->everyMinute();
        $schedule->command('inspect:followback')->hourlyAt(15);
        $schedule->command('inspect:active')->hourlyAt(30);
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
