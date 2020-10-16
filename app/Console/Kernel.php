<?php

namespace App\Console;

use App\Console\Commands\RemainUnchangedCommand;
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
        RemainUnchangedCommand::class
    ];

    /**
     * @param Schedule $schedule
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('remain_unchanged')
            ->cron('0 11 * * SAT');
    }
}
