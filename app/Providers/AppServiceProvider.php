<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use phpseclib\Crypt\AES;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->aes();
    }

    protected function aes()
    {
        $this->app->singleton('aes', function ($app) {
            $key = $app->config->get('app.key');

            if (Str::startsWith($key, 'base64:')) {
                $key = base64_decode(substr($key, 7));
            }

            $aes =  new AES();

            $aes->setKey($key);

            return $aes;
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(): void
    {
        Carbon::setLocale('zh_TW');
    }
}
