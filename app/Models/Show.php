<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;

/**
 * App\Models\Show
 *
 * @property string $id
 * @property string $title
 * @property string $slug
 * @property string|null $poster
 * @property int|null $start_year
 * @property int|null $end_year
 * @property string|null $summary
 * @property string $status
 * @property int $thetvdb_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $library_id
 * @property-read \App\Models\Library|null $library
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Season[] $seasons
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BaseModel disableCache()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Show newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Show newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Show query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Show whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Show whereEndYear($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Show whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Show whereLibraryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Show wherePoster($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Show whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Show whereStartYear($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Show whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Show whereSummary($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Show whereThetvdbId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Show whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Show whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BaseModel withCacheCooldownSeconds($seconds)
 * @mixin \Eloquent
 */
class Show extends BaseModel
{
    public const Ongoing = 'ongoing';
    public const Ended = 'ended';
    public const Cancelled = 'cancelled';
    public const Unknown = 'unknown';

    protected $fillable = [
        'title',
        'slug',
        'poster',
        'start_year',
        'end_year',
        'summary',
        'status',
        'thetvdb_id',
        'library_id',
    ];

    public function library()
    {
        return $this->belongsTo(Library::class);
    }

    public function seasons()
    {
        return $this->hasMany(Season::class);
    }
}
