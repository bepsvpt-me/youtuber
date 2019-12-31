<?php

namespace App\Console\Commands\YouTube\Playlist;

use App\Channel;
use App\Console\Commands\YouTube\YouTube;
use App\Video;
use Carbon\Carbon;
use Google_Service_YouTube_PlaylistItem;

class Import extends YouTube
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'youtube:playlist:import {id : YouTube playlist id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '更新頻道影片清單資訊';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        /** @var Channel|null $channel */

        $channel = Channel::query()->where('playlist', '=', $this->argument('id'))->first();

        if (is_null($channel)) {
            $this->error('不存在的 playlist id');

            return;
        }

        $nextPageToken = '';

        while (true) {
            $playlist = $this->youtube
                ->playlistItems
                ->listPlaylistItems('snippet', [
                    'playlistId' => $this->argument('id'),
                    'maxResults' => 50,
                    'pageToken' => $nextPageToken,
                ]);

            /** @var Google_Service_YouTube_PlaylistItem $video */

            $uids = [];

            foreach ($playlist->getItems() as $video) {
                $snippet = $video->getSnippet();

                $uid = $snippet->getResourceId()->getVideoId();

                if (Video::query()->where('uid', '=', $uid)->exists()) {
                    if (!empty($uids)) {
                        $this->call('youtube:video:statistics', ['id' => $uids]);
                    }

                    break 2;
                }

                $channel->videos()->create([
                    'uid' => $uid,
                    'name' => $snippet->getTitle(),
                    'description' => $snippet->getDescription(),
                    'views' => 0,
                    'likes' => 0,
                    'dislikes' => 0,
                    'favorites' => 0,
                    'comments' => 0,
                    'published_at' => Carbon::parse($snippet->getPublishedAt()),
                    'updated_at' => $this->now,
                ]);

                $uids[] = $uid;

                $this->info(sprintf('%s（%s）新增成功', $snippet->getTitle(), $uid));
            }

            if (!empty($uids)) {
                $this->call('youtube:video:statistics', ['id' => $uids]);
            }

            if (is_null($nextPageToken = $playlist->getNextPageToken())) {
                break;
            }
        }
    }
}
