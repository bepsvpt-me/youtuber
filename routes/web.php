<?php

use Illuminate\Support\Facades\Route;

Route::prefix('safe-browse')
    ->name('ytimg')
    ->get('ytimg-{payload}')
    ->uses( 'SafeBrowseController@ytimg');

Route::prefix('safe-browse')
    ->name('ggpht')
    ->get('ggpht-{payload}')
    ->uses('SafeBrowseController@ggpht');

Route::name('home')
    ->get('/')
    ->uses('HomeController@index');

Route::name('trending')
    ->get('trending')
    ->uses('TrendingController@index');

Route::name('trending.time')
    ->get('trending/{time}')
    ->uses('TrendingController@show');

Route::name('channel')
    ->get('{channel}')
    ->uses('ChannelController@index');

Route::name('video')
    ->get('{channel}/{video}')
    ->uses('VideoController@index');

Route::name('video.date')
    ->get('{channel}/{video}/{date}')
    ->uses('VideoController@show');
