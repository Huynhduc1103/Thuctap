<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        'App\console\Commands\ScheduleFailed',
        'App\Console\Commands\SendEvent',
        'App\Console\Commands\SendBirthday',
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('message:birthday')->dailyAt('07:00')->withoutOverlapping();
        //->everyMinute()->withoutOverlapping();
        $schedule->command('message:event')->everyMinute()->withoutOverlapping();
        $schedule->command('failed:send')->everyMinute()->withoutOverlapping();
    }
}
