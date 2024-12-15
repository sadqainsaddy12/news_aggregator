<?php

namespace App\Console;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Artisan;

class Kernel extends ConsoleKernel
{

    protected $commands = [
        \App\Console\Commands\FetchNewsApi::class,
        \App\Console\Commands\FetchNYTNewsApi::class,
        \App\Console\Commands\FetchTheGuardainNewsApi::class,
    ];

    protected function schedule(Schedule $schedule) {
       $schedule->command('app:fetch-news-api')->everySecond();
       $schedule->command('app:fetch-NYT-news-api')->everySecond();
       $schedule->command('app:fetch-guardian-news-api')->everySecond();

    //    $schedule->command('app:fetch-NYT-news-api')->daily();
    //    $schedule->command('app:fetch-guardian-news-api')->daily();
    //    $schedule->command('app:fetch-news-api')->daily();
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
