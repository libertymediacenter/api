<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Subtitle
 *
 * @property int $id
 * @property string $media_container_id
 * @property string|null $codec
 * @property string|null $language_code
 * @property string|null $display_title
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Subtitle newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Subtitle newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Subtitle query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Subtitle whereCodec($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Subtitle whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Subtitle whereDisplayTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Subtitle whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Subtitle whereLanguageCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Subtitle whereMediaContainerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Subtitle whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Subtitle extends Model
{
    //
}
