<?php

use App\Channel;
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
        'videos' => $channel->videos()->get(),
    ]);
});

Route::name('video')->get('{channel}/{video}', function (string $cid, string $vid) {
    $channel = Channel::query()->where('uid', '=', $cid)->firstOrFail();

    $video = $channel->videos()
        ->where('uid', '=', $vid)
        ->firstOrFail();

    return view('video', [
        'channel' => $channel,
        'video' => $video,
        'statistics' => $video->statistics,
    ]);
});

Route::name('video.date')->get('{channel}/{video}/{date}', function (string $cid, string $vid, string $date) {
    try {
        $day = Carbon::parse($date)->startOfDay()->subHours(8);
    } catch (Exception $e) {
        abort(404);
    }

    $channel = Channel::query()->where('uid', '=', $cid)->firstOrFail();

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
});
