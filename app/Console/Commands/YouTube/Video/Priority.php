<?php

namespace App\Console\Commands\YouTube\Video;

use App\Video;
use Illuminate\Console\Command;

class Priority extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'youtube:video:priority';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '調整影片權重';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $videos = Video::query()
            ->where('deleted', false)
            ->orderByDesc('published_at')
            ->get(['id', 'channel_id', 'name', 'views', 'priority', 'published_at']);

        foreach ($videos as $video) {
            if ($video->priority < 100) {
                continue;
            }

            if ($video->published_at->diffInWeeks() <= 1) {
                $video->priority = 50000;
            } else {
                $video->priority = 100
                    + max(15000 - $video->published_at->diffInDays() * 10, 0)
                    + intval($video->views / 1000);
            }

            $video->save();
        }
    }
}
