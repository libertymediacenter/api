<?php

namespace App\Models;

/**
 * App\Models\Show
 *
 * @property string $id
 * @property string $title
 * @property string $slug
 * @property string|null $poster
 * @property int|null $start_year
 * @property int|null $end_year
 * @property string|null $summary
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Season[] $seasons
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Show newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Show newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Show query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Show whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Show whereEndYear($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Show whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Show wherePoster($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Show whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Show whereStartYear($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Show whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Show whereSummary($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Show whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Show whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Show extends BaseModel
{
    public const Ongoing = 'ongoing';
    public const Ended = 'ended';
    public const Cancelled = 'cancelled';
    public const Unknown = 'unknown';

    public function seasons()
    {
        return $this->hasMany(Season::class);
    }
}
