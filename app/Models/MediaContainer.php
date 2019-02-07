<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\MediaContainer
 *
 * @property string $id
 * @property string $path
 * @property string|null $container
 * @property int|null $bitrate
 * @property int|null $duration
 * @property int|null $size
 * @property string $media_type
 * @property string $media_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Audio[] $audios
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Subtitle[] $subtitles
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Video[] $videos
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BaseModel disableCache()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MediaContainer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MediaContainer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MediaContainer query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MediaContainer whereBitrate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MediaContainer whereContainer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MediaContainer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MediaContainer whereDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MediaContainer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MediaContainer whereMediaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MediaContainer whereMediaType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MediaContainer wherePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MediaContainer whereSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MediaContainer whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BaseModel withCacheCooldownSeconds($seconds)
 * @mixin \Eloquent
 */
class MediaContainer extends BaseModel
{
    protected $fillable = [
        'media_id',
        'media_type',
        'duration',
        'size',
        'bitrate',
        'path',
    ];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function audios()
    {
        return $this->hasMany(Audio::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function videos()
    {
        return $this->hasMany(Video::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subtitles()
    {
        return $this->hasMany(Subtitle::class);
    }
}
