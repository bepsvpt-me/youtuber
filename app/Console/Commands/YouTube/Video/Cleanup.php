<?php

namespace App\Console\Commands\YouTube\Video;

use App\Console\Commands\YouTube\YouTube;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use stdClass;

final class Cleanup extends YouTube
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'youtube:video:cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '移除影片重複統計資訊';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        ini_set('memory_limit', '256M');

        $videoIds = $this->videoIds();

        $this->output->progressStart(count($videoIds));

        foreach ($videoIds as $videoId) {
            $duplicates = [];

            $statistics = $this->statistics($videoId);

            $statistics->pop();

            $previous = $statistics->shift();

            foreach ($statistics as $statistic) {
                if ($this->same($statistic, $previous)) {
                    $duplicates[] = $statistic->id;
                }

                $previous = $statistic;
            }

            $this->remove($duplicates);

            $this->output->progressAdvance();
        }

        $this->output->progressFinish();

        $this->info('重複影片統計資料清除成功');
    }

    /**
     * Get video ids.
     *
     * @return array
     */
    protected function videoIds(): array
    {
        return DB::table('videos')
            ->orderBy('id')
            ->get('id')
            ->pluck('id')
            ->toArray();
    }

    /**
     * Get video statistics by id.
     *
     * @param int $videoId
     *
     * @return Collection
     */
    protected function statistics(int $videoId): Collection
    {
        return DB::table('video_statistics')
            ->where('video_id', '=', $videoId)
            ->orderBy('fetched_at')
            ->get();
    }

    /**
     * Check two element is same or not.
     *
     * @param stdClass $first
     * @param stdClass $second
     *
     * @return bool
     */
    protected function same(stdClass $first, stdClass $second): bool
    {
        $fields = ['views', 'likes', 'dislikes', 'favorites', 'comments'];

        foreach ($fields as $field) {
            if ($first->{$field} !== $second->{$field}) {
                return false;
            }
        }

        return true;
    }

    /**
     * Remove video duplicates statistics.
     *
     * @param array $ids
     *
     * @return void
     */
    protected function remove(array $ids): void
    {
        foreach (array_chunk($ids, 150) as $id) {
            DB::table('video_statistics')
                ->whereIn('id', $id)
                ->delete();
        }
    }
}
