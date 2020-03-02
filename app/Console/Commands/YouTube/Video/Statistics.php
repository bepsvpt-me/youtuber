<?php

namespace App\Console\Commands\YouTube\Video;

use App\Console\Commands\YouTube\YouTube;
use App\Video;
use Google_Service_YouTube_Video;

final class Statistics extends YouTube
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'youtube:video:statistics {id* : YouTube video id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '更新影片統計資訊';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $videos = Video::query()->whereIn('uid', $this->argument('id'))->get();

        if ($videos->isEmpty()) {
            return;
        } else if ($videos->count() > 50) {
            $this->error('Max videos number is 50.');

            return;
        }

        /** @var Google_Service_YouTube_Video[] $items */

        $items = $this->youtube
            ->videos
            ->listVideos('statistics', ['id' => $videos->pluck('uid')->implode(',')])
            ->getItems();

        foreach ($items as $item) {
            $statistics = $item->getStatistics();

            $fields = [
                'views' => $statistics->getViewCount() ?: 0,
                'likes' => $statistics->getLikeCount() ?: 0,
                'dislikes' => $statistics->getDislikeCount() ?: 0,
                'favorites' => $statistics->getFavoriteCount() ?: 0,
                'comments' => $statistics->getCommentCount() ?: 0,
            ];

            /** @var Video $video */

            $video = $videos->firstWhere('uid', '=', $item->getId());

            $video->update(array_merge($fields, ['updated_at' => $this->now]));

            $video->statistics()->create(array_merge($fields, ['fetched_at' => $this->now]));

            $this->info(sprintf('%s（%s） 更新成功', $video->name, $item->getId()));
        }
    }
}
