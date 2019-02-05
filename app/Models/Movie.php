<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;

/**
 * App\Models\Movie
 *
 * @property string $id
 * @property string $title
 * @property string $slug
 * @property int|null $year
 * @property mixed|null $released
 * @property int|null $runtime
 * @property string|null $tagline
 * @property string|null $summary
 * @property string|null $plot
 * @property string|null $poster
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $library_id
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Person[] $cast
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Genre[] $genres
 * @property-read \App\Models\Library|null $library
 * @property-read \App\Models\MediaContainer $media
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Rating[] $ratings
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BaseModel disableCache()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Movie hasGenre($genre)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Movie newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Movie newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Movie query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Movie whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Movie whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Movie whereLibraryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Movie wherePlot($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Movie wherePoster($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Movie whereReleased($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Movie whereRuntime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Movie whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Movie whereSummary($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Movie whereTagline($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Movie whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Movie whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Movie whereYear($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BaseModel withCacheCooldownSeconds($seconds)
 * @mixin \Eloquent
 */
class Movie extends BaseModel
{
    protected $fillable = [
        'title',
        'slug',
        'year',
        'released',
        'runtime',
        'tagline',
        'summary',
        'plot',
        'poster',
    ];

    protected $casts = [
        'released' => 'datetime:Y-m-d',
    ];

    protected $with = ['genres', 'ratings', 'cast'];

    // Scopes

    public function scopeHasGenre(Builder $query, string $genre)
    {
        return $query->whereHas('genres', function (Builder $query) use ($genre) {
            $query->where('name', '=', $genre);
        });
    }

    // Relations

    public function cast()
    {
        return $this->belongsToMany(
            Person::class,
            'cast',
            'media_id',
            'people_id'
        )
            ->using(CastPivot::class)
            ->withPivot('role');
    }

    public function genres()
    {
        return $this->belongsToMany(
            Genre::class,
            'genre_to_media',
            'model_id',
            'genre_id'
        )->using(GenrePivot::class);
    }

    public function library()
    {
        return $this->belongsTo(Library::class);
    }

    public function media()
    {
        return $this->morphOne(MediaContainer::class, 'media');
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class, 'model_id', 'id');
    }
}
