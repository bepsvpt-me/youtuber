<?php

namespace App\Console\Commands\YouTube\Channel;

use App\Channel;
use App\Console\Commands\YouTube\YouTube;
use Google_Service_YouTube_Channel;

class Statistics extends YouTube
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'youtube:channel:statistics {id* : YouTube channel id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '更新 YouTuber 統計資訊';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $channels = Channel::query()->whereIn('uid', $this->argument('id'))->get();

        if ($channels->isEmpty()) {
            return;
        }

        /** @var Google_Service_YouTube_Channel[] $items */

        $items = $this->youtube
            ->channels
            ->listChannels('statistics', ['id' => $channels->pluck('uid')->implode(',')])
            ->getItems();

        foreach ($items as $item) {
            $statistics = $item->getStatistics();

            $fields = [
                'subscribers' => $statistics->getSubscriberCount(),
                'views' => $statistics->getViewCount(),
                'videos' => $statistics->getVideoCount(),
                'comments' => $statistics->getCommentCount(),
            ];

            /** @var Channel $channel */

            $channel = $channels->firstWhere('uid', '=', $item->getId());

            $channel->update(array_merge($fields, ['updated_at' => $this->now]));

            $channel->statistics()->create(array_merge($fields, ['fetched_at' => $this->now]));
        }
    }
}
