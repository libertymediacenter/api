<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Audio
 *
 * @property int $id
 * @property string $media_container_id
 * @property string $display_name
 * @property int $stream_index
 * @property int|null $bitrate
 * @property string|null $codec
 * @property string|null $language_code
 * @property int|null $channels
 * @property string|null $profile
 * @property string|null $channel_layout
 * @property int|null $sampling_rate
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Audio newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Audio newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Audio query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Audio whereBitrate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Audio whereChannelLayout($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Audio whereChannels($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Audio whereCodec($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Audio whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Audio whereDisplayName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Audio whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Audio whereLanguageCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Audio whereMediaContainerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Audio whereProfile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Audio whereSamplingRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Audio whereStreamIndex($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Audio whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Audio extends Model
{
    //
}
