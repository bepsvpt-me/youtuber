<?php

use App\Channel;
use App\Video;
use Carbon\Carbon;
use Illuminate\Support\Facades\Route;

Carbon::setLocale('zh_TW');

Route::name('home')->get('/', function () {
    return view('home', [
        'channels' => Channel::all(),
    ]);
});

Route::name('channel')->get('{channel}', function (string $cid) {
    $channel = Channel::query()->where('uid', '=', $cid)->firstOrFail();

    return view('channel', [
        'channel' => $channel,
        'videos' => $channel->videos()->orderByDesc('published_at')->get(),
    ]);
});

Route::name('video')->get('{channel}/{video}', function (string $cid, string $vid) {
    $channel = Channel::query()->where('uid', '=', $cid)->firstOrFail();

    /** @var Video $video */

    $video = Video::query()
        ->where('channel_id', '=', $channel->getKey())
        ->where('uid', '=', $vid)
        ->firstOrFail();

    $statistics = $video->statistics()->get()->unique('views');

    if ($statistics->count() > 160) {
        $last = $statistics->pop();

        $statistics = $statistics->nth(ceil($statistics->count() / 160));

        $statistics->push($last);
    }

    return view('video', [
        'channel' => $channel,
        'video' => $video,
        'statistics' => $statistics,
    ]);
});

Route::name('video.date')->get('{channel}/{video}/{date}', function (string $cid, string $vid, string $date) {
    try {
        $day = Carbon::parse($date)->startOfDay()->subHours(8);
    } catch (Exception $e) {
        abort(404);
    }

    $channel = Channel::query()->where('uid', '=', $cid)->firstOrFail();

    $video = Video::query()
        ->where('channel_id', '=', $channel->getKey())
        ->where('uid', '=', $vid)
        ->firstOrFail();

    $statistics = $video->statistics()
        ->whereBetween('fetched_at', [$day, $day->clone()->addDay()])
        ->get()
        ->unique('views');

    return view('video', [
        'channel' => $channel,
        'video' => $video,
        'statistics' => $statistics,
    ]);
});
