<?php

namespace App\Console;

use App\Channel;
use App\Video;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Database\Eloquent\Collection;
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
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param Schedule $schedule
     *
     * @return void
     */
    protected function schedule(Schedule $schedule): void
    {
        $chunks = $this->videos();

        $intervals = $this->intervals();

        foreach ($chunks as $idx => $videos) {
            $schedule->command('youtube:video:statistics', $videos)->{$intervals[$idx] ?? 'daily'}();
        }

        foreach ($this->playlists() as $playlist) {
            if (!is_null($playlist->crontab)) {
                foreach (json_decode($playlist->crontab, true) as $cron) {
                    $schedule->command('youtube:playlist:import', ['--id' => $playlist->playlist])->{array_shift($cron)}(...$cron);
                }
            }

            $schedule->command('youtube:playlist:import', ['--id' => $playlist->playlist])->daily();
        }

        $schedule->command('youtube:video:trending')->everyFifteenMinutes();

        $schedule->command('youtube:channel:statistics', $this->channels())->hourly();

        $schedule->command('youtube:video:priority')->daily();

        $schedule->command('youtube:video:cleanup')->weekly();
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
     * Get playlist crontab.
     *
     * @return Collection
     */
    protected function playlists(): Collection
    {
        return Channel::query()->get(['playlist', 'crontab']);
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
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');
    }
}
