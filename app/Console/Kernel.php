<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Http\Controllers\StaticMembersController;
use Illuminate\Support\Facades\DB;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\Inspire::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function()
        {
            StaticMembersController::executeRafflesJob();
            StaticMembersController::executeRefundsJob();
        })->everyMinute();
        
        $schedule->call(function()
        {
            DB::statement('call `execute_maintenance`');
        })->dailyAt('00:00');
        
        $schedule->command('queue:work')->everyMinute();
    }
}
