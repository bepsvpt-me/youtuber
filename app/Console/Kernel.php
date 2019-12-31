<?php

namespace App\Console;

use App\Channel;
use App\Video;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Arr;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\YouTube\Channel\Add::class,
        Commands\YouTube\Channel\Statistics::class,
        Commands\YouTube\Playlist\Import::class,
        Commands\YouTube\Video\Priority::class,
        Commands\YouTube\Video\Statistics::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $chunks = $this->videos();

        $intervals = $this->intervals();

        foreach ($chunks as $idx => $videos) {
            $schedule->command('youtube:video:statistics', $videos)->{$intervals[$idx] ?? 'daily'}();
        }

        $schedule->command('youtube:channel:statistics', $this->channels())->hourly();

        $schedule->command('youtube:video:priority')->daily();
    }

    /**
     * Get array of channel uid.
     *
     * @return array
     */
    protected function channels(): array
    {
        return Channel::query()
            ->pluck('uid')
            ->chunk(50)
            ->map->values()
            ->map->prepend('--')
            ->toArray();
    }

    /**
     * Get array of video uid.
     *
     * @return array
     */
    protected function videos(): array
    {
        return Video::query()
            ->where('deleted', false)
            ->orderByDesc('priority')
            ->orderByDesc('published_at')
            ->get()
            ->pluck('uid')
            ->chunk(50)
            ->map->values()
            ->map->prepend('--')
            ->toArray();
    }

    /**
     * Get each video chunk interval.
     *
     * @return array
     */
    protected function intervals(): array
    {
        $intervals = [
            'everyMinute',
            'everyFiveMinutes',
            'everyTenMinutes',
            'everyFifteenMinutes',
            array_fill(0, 10, 'everyThirtyMinutes'),
            array_fill(0, 30, 'hourly'),
        ];

        return Arr::flatten($intervals);
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
