<?php

namespace App\Models;

/**
 * App\Models\Person
 *
 * @property string $id
 * @property string $imdb_id
 * @property string $name
 * @property string|null $slug
 * @property string|null $photo
 * @property string|null $bio
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Person newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Person newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Person query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Person whereBio($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Person whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Person whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Person whereImdbId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Person whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Person wherePhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Person whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Person whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Person extends BaseModel
{
    protected $table = 'people';

    protected $fillable = [
        'imdb_id',
        'name',
        'slug',
        'photo',
        'bio',
    ];
}
