<?php

namespace App\Console\Commands\YouTube;

use Google_Service_YouTube;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

abstract class YouTube extends Command
{
    /**
     * @var Google_Service_YouTube
     */
    protected $youtube;

    /**
     * @var Carbon
     */
    protected $now;

    /**
     * YouTube constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->youtube = app('youtube');

        $this->now = now();
    }
}
