<?php

use App\Channel;
use App\Video;
use Illuminate\Support\Facades\Route;

Carbon\Carbon::setLocale('zh_TW');

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

    $video = Video::query()
        ->where('channel_id', '=', $channel->getKey())
        ->where('uid', '=', $vid)
        ->firstOrFail();

    return view('video', [
        'channel' => $channel,
        'video' => $video,
        'statistics' => $video->statistics()->orderBy('fetched_at')->get()->unique('views'),
    ]);
});
