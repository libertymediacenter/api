<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * App\Models\GenrePivot
 *
 * @property int $id
 * @property string $model_id
 * @property string $genre_id
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GenrePivot newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GenrePivot newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GenrePivot query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GenrePivot whereGenreId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GenrePivot whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GenrePivot whereModelId($value)
 * @mixin \Eloquent
 */
class GenrePivot extends Pivot
{
    protected $table = 'genre_to_media';
}
