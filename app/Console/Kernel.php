<?php

namespace App\Console;

//use App\Jobs\RetrieveUiPathOrchestratorAlerts;
use App\Models\AIBasedAlertTrigger;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Artisan;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('horizon:snapshot')->everyMinute();
        //$schedule->job(new RetrieveUiPathOrchestratorAlerts)->everyMinute();

        $triggers = AIBasedAlertTrigger::all();
        foreach($triggers as $trigger) {
            foreach ($trigger->crons as $index => $cron) {
                //$schedule->command('trigger:manage', [$trigger->id])->cron($cron['cron']);
                $schedule->call(function () use ($trigger, $index) {
                    Artisan::queue('trigger:manage', [
                        'id' => $trigger->id,
                        'cron_index' => $index,
                    ]);
                })->cron($cron['cron']);
            }
        }
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
