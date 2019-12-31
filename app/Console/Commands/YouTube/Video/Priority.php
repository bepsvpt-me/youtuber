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
            ->where('priority', '>=', 100)
            ->orderByDesc('published_at')
            ->get(['id', 'views', 'comments', 'likes', 'dislikes', 'priority', 'published_at']);

        foreach ($videos as $video) {
            $priority = 100
                + $video->published_at->timestamp
                + $video->views
                + $video->comments * 200
                + $video->dislikes * 100
                + $video->likes * 50
                ;

            $video->update(compact('priority'));
        }
    }
}
