<?php

namespace App\Providers;

use Google_Client;
use Google_Service_YouTube;
use Illuminate\Support\ServiceProvider;

final class GoogleServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('google', function () {
            return new Google_Client([
                'developer_key' => config('services.google.key'),
            ]);
        });

        $this->app->singleton('youtube', function () {
            return new Google_Service_YouTube($this->app['google']);
        });
    }
}
