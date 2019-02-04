<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * App\Models\MediaPerson
 *
 * @property int $id
 * @property string $media_id
 * @property string $people_id
 * @property string $role
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CastPivot newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CastPivot newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CastPivot query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CastPivot whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CastPivot whereMediaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CastPivot wherePeopleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CastPivot whereRole($value)
 * @mixin \Eloquent
 */
class CastPivot extends Pivot
{
    protected $table = 'cast';

    protected $fillable = [
        'role',
    ];
}
