<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Channel extends Model
{
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'published_at',
        'updated_at',
    ];

    /**
     * 頻道統計.
     *
     * @return HasMany
     */
    public function statistics(): HasMany
    {
        return $this->hasMany(ChannelStatistic::class);
    }

    /**
     * 頻道影片.
     *
     * @return HasMany
     */
    public function videos(): HasMany
    {
        return $this->hasMany(Video::class)
            ->orderByDesc('published_at');
    }
}
