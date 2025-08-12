<?php

namespace App\Console;

use App\Models\Item;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Carbon\Carbon;

class Kernel extends ConsoleKernel
{

    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('notify:end-auction')->everyMinute()->withoutOverlapping();
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
