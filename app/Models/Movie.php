<?php

namespace App\Models;

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
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Genre[] $genres
 * @property-read \App\Models\Library|null $library
 * @property-read \App\Models\MediaContainer $media
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $ratings
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

    protected $with = ['genres', 'ratings'];

    public function genres()
    {
        return $this->belongsToMany(
            Genre::class,
            'genre_to_media',
            'model_id',
            'genre_id')->using(GenrePivot::class);
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
