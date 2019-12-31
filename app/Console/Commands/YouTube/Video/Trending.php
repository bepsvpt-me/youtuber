<?php

namespace App\Console\Commands\YouTube\Video;

use App\Console\Commands\YouTube\YouTube;
use Google_Service_YouTube_Video;
use Illuminate\Support\Facades\DB;

class Trending extends YouTube
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'youtube:video:trending';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '更新發燒影片清單';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        /** @var Google_Service_YouTube_Video[] $items */

        $items = $this->youtube
            ->videos
            ->listVideos('snippet', [
                'chart' => 'mostPopular',
                'maxResults' => 50,
                'regionCode' => 'TW',
            ])
            ->getItems();

        $now = now()->toDateTimeString();

        foreach ($items as $idx => $item) {
            DB::table('trendings')->insert([
                'vid' => $item->getId(),
                'ranking' => $idx + 1,
                'fetched_at' => $now,
            ]);
        }

        $this->info('發燒影片清單更新成功');
    }
}
