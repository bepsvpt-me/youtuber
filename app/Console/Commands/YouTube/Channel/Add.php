<?php

namespace App\Console\Commands\YouTube\Channel;

use App\Channel;
use App\Console\Commands\YouTube\YouTube;
use Carbon\Carbon;
use Google_Service_YouTube_Channel;

class Add extends YouTube
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'youtube:channel:add {id* : YouTube channel id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '新增 YouTuber';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $ids = array_filter($this->argument('id'), function (string $uid) {
            $exists = Channel::query()->where('uid', '=', $uid)->exists();

            if ($exists) {
                $this->comment(sprintf('%s 已存在', $uid));
            }

            return !$exists;
        });

        if (empty($ids)) {
            return;
        }

        $part = implode(',', [
            'snippet',
            'contentDetails',
        ]);

        /** @var Google_Service_YouTube_Channel[] $channels */

        $channels = $this->youtube
            ->channels
            ->listChannels($part, ['id' => implode(',', $ids)])
            ->getItems();

        foreach ($channels as $channel) {
            Channel::query()->create([
                'uid' => $channel->getId(),
                'name' => $channel->getSnippet()->getTitle(),
                'playlist' => $channel->getContentDetails()->getRelatedPlaylists()->getUploads(),
                'subscribers' => 0,
                'views' => 0,
                'videos' => 0,
                'comments' => 0,
                'published_at' => Carbon::parse($channel->getSnippet()->getPublishedAt()),
                'updated_at' => $this->now,
            ]);

            $this->info(sprintf('%s（%s）新增成功', $channel->getSnippet()->getTitle(), $channel->getId()));
        }
    }
}
