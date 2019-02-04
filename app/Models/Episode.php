<?php

namespace App\Models;

/**
 * App\Models\Episode
 *
 * @property string $id
 * @property string $season_id
 * @property string|null $title
 * @property string|null $summary
 * @property int|null $runtime
 * @property string|null $poster
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\MediaContainer[] $media
 * @property-read \App\Models\Season $season
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Episode newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Episode newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Episode query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Episode whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Episode whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Episode wherePoster($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Episode whereRuntime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Episode whereSeasonId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Episode whereSummary($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Episode whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Episode whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Episode extends BaseModel
{

    public function media()
    {
        return $this->morphToMany(MediaContainer::class, 'media');
    }

    public function season()
    {
        return $this->belongsTo(Season::class);
    }
}
