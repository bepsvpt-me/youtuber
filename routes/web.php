<?php

use App\Channel;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Carbon::setLocale('zh_TW');

Route::name('home')->get('/', function () {
    return view('home', [
        'channels' => Channel::query()->where('hidden', false)->get(),
    ]);
});

Route::prefix('safe-browse')->group(function () {
    Route::name('ytimg')->get('ytimg-{payload}')->uses( 'SafeBrowseController@ytimg');
    Route::name('ggpht')->get('ggpht-{payload}')->uses('SafeBrowseController@ggpht');
});

Route::name('trending')->get('trending', function () {
    $videos = DB::table('trendings')
        ->orderByDesc('fetched_at')
        ->orderBy('ranking')
        ->take(50)
        ->get();

    return view('trending', ['videos' => $videos]);
});

Route::name('trending.time')->get('trending/{time}', function (string $time) {
    try {
        $carbon = Carbon::parse($time)->setSecond(0);

        abort_if($carbon->isFuture(), 404);

        $carbon->subMinutes($carbon->minute % 15);
    } catch (Exception $e) {
        abort(404);
    }

    $lower = $carbon->subMinutes(1)->format('Y-m-d H:i:s');

    $upper = $carbon->addMinutes(6)->format('Y-m-d H:i:s');

    $videos = DB::table('trendings')
        ->whereBetween('fetched_at', [$lower, $upper])
        ->orderByDesc('fetched_at')
        ->orderBy('ranking')
        ->take(50)
        ->get();

    return view('trending', ['videos' => $videos]);
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
