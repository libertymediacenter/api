<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class CastResource
 * @package App\Http\Resources
 *
 * @mixin \App\Models\Person
 * @mixin \App\Models\CastPivot
 */
class CastResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'imdbId' => (string)$this->imdb_id,
            'name'   => (string)$this->name,
            'slug'   => (string)$this->slug,
            'photo'  => (string)$this->photo,
            'role'   => $this->role ?? $this->pivot->role,
        ];
    }
}
