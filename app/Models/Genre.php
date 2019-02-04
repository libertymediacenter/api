<?php

namespace App\Models;

/**
 * App\Models\Genre
 *
 * @property string $id
 * @property string $name
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Genre newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Genre newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Genre query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Genre whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Genre whereName($value)
 * @mixin \Eloquent
 */
class Genre extends BaseModel
{
    public $timestamps = false;

    protected $fillable = [
        'name',
    ];

    public function movies()
    {
        return $this->belongsToMany(
            Movie::class,
            'genre_to_media',
            'model_id',
            'genre_id')->using(GenrePivot::class);
    }
}
