<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class GenreResource
 * @package App\Http\Resources
 *
 * @mixin \App\Models\Genre
 * @mixin \App\Models\GenrePivot
 */
class GenreResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'name' => $this->name,
        ];
    }
}
