<?php

namespace App\Console;

use App\Console\Commands\AutoReportCommand;
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
        RemainUnchangedCommand::class,
        AutoReportCommand::class,
    ];

    /**
     * @param Schedule $schedule
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('remain_unchanged')
            ->weekly()
            ->mondays()
            ->at('01:00');

        $schedule->command('auto_report')
            ->weekdays()
            ->at('16:45');
    }
}
