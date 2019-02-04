<?php

namespace App\Models;

/**
 * App\Models\Video
 *
 * @property string $id
 * @property string $media_container_id
 * @property string $display_name
 * @property int $stream_index
 * @property int|null $bitrate
 * @property string|null $framerate
 * @property int|null $height
 * @property int|null $width
 * @property string|null $codec
 * @property string|null $chroma_location
 * @property string|null $color_primaries
 * @property string|null $color_range
 * @property string|null $profile
 * @property string|null $scan_type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video whereBitrate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video whereChromaLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video whereCodec($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video whereColorPrimaries($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video whereColorRange($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video whereDisplayName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video whereFramerate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video whereHeight($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video whereMediaContainerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video whereProfile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video whereScanType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video whereStreamIndex($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video whereWidth($value)
 * @mixin \Eloquent
 */
class Video extends BaseModel
{
    protected $fillable = [
        'media_container_id',
        'display_name',
        'stream_index',
        'bitrate',
        'framerate',
        'height',
        'width',
        'codec',
        'chroma_location',
        'color_range',
        'profile',
        'scan_type',
    ];
}
