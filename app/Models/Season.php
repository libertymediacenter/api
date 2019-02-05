<?php

namespace App\Models;

/**
 * App\Models\Season
 *
 * @property string $id
 * @property string $show_id
 * @property int $season
 * @property string|null $poster
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Episode[] $episodes
 * @property-read \App\Models\Show $show
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BaseModel disableCache()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Season newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Season newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Season query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Season whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Season whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Season wherePoster($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Season whereSeason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Season whereShowId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Season whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BaseModel withCacheCooldownSeconds($seconds)
 * @mixin \Eloquent
 */
class Season extends BaseModel
{

    protected $fillable = [
        'show_id',
        'season',
        'poster',
    ];

    public function show()
    {
        return $this->belongsTo(Show::class);
    }

    public function episodes()
    {
        return $this->hasMany(Episode::class);
    }
}
