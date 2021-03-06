<?php

namespace Birdmin\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     * @var array
     */
    protected $commands = [
        'Birdmin\Console\Commands\LaunchCommand',
        'Birdmin\Console\Commands\RefuelCommand',
        'Birdmin\Console\Commands\ImportCommand',
        'Birdmin\Console\Commands\SitemapCommand',
    ];

    /**
     * Define the application's command schedule.
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {

    }
}
