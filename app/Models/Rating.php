<?php

namespace App\Models;

use Illuminate\Support\Arr;

/**
 * App\Models\Rating
 *
 * @property string $id
 * @property string $model_id
 * @property string $model_type
 * @property string $provider_id
 * @property string $provider
 * @property int $score
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Rating newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Rating newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Rating query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Rating whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Rating whereModelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Rating whereModelType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Rating whereProvider($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Rating whereProviderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Rating whereScore($value)
 * @mixin \Eloquent
 */
class Rating extends BaseModel
{
    public const IMDB = 'imdb';
    public const TMDB = 'tmdb';
    public const RottenTomatoes = 'rottentomatoes';
    public const MetaCritic = 'metacritic';

    public $timestamps = false;

    protected $fillable = [
        'model_id',
        'model_type',
        'provider_id',
        'provider',
        'score',
    ];
}
