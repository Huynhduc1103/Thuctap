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
        'App\Console\Commands\SendEmails',
        'App\Console\Commands\SendEmailEvent',
        'App\Console\Commands\SmsBirthday'
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        //$schedule->command('User:email')->everyMinute()->withoutOverlapping();
        $schedule->command('User:sms')->everyMinute()->withoutOverlapping();
       // $schedule->command('message:sendevent')->everyMinute()->withoutOverlapping();
    }
}
