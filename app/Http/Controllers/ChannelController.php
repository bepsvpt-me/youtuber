<?php

namespace App\Http\Controllers;

use App\Channel;
use Illuminate\View\View;

class ChannelController extends Controller
{
    /**
     * Channel statistic page.
     *
     * @param string $cid
     *
     * @return View
     */
    public function index(string $cid): View
    {
        /** @var Channel $channel */

        $channel = Channel::query()
            ->where('uid', '=', $cid)
            ->firstOrFail();

        return view('channel', [
            'channel' => $channel,
            'videos' => $channel->videos()->get(),
        ]);
    }
}
