<?php

namespace App\Http\Controllers;

use App\Channel;
use App\Video;
use Carbon\Carbon;
use Exception;
use Illuminate\View\View;

final class VideoController extends Controller
{
    /**
     * Video statistic page.
     *
     * @param string $cid
     * @param string $vid
     *
     * @return View
     */
    public function index(string $cid, string $vid): View
    {
        /** @var Channel $channel */

        $channel = Channel::query()
            ->where('uid', '=', $cid)
            ->firstOrFail();

        $video = $channel->videos()
            ->where('uid', '=', $vid)
            ->firstOrFail();

        return view('video', [
            'channel' => $channel,
            'video' => $video,
            'statistics' => $video->statistics,
        ]);
    }

    /**
     * Video specific date page.
     *
     * @param string $cid
     * @param string $vid
     * @param string $date
     *
     * @return View
     */
    public function show(string $cid, string $vid, string $date): View
    {
        try {
            $day = Carbon::parse($date)->startOfDay()->subHours(8);
        } catch (Exception $e) {
            abort(404);
        }

        /** @var Channel $channel */

        $channel = Channel::query()
            ->where('uid', '=', $cid)
            ->firstOrFail();

        /** @var Video $video */

        $video = $channel->videos()
            ->where('uid', '=', $vid)
            ->firstOrFail();

        $statistics = $video->statistics()
            ->whereBetween('fetched_at', [$day, $day->clone()->addDay()])
            ->get();

        return view('video', [
            'channel' => $channel,
            'video' => $video,
            'statistics' => $statistics,
        ]);
    }
}
