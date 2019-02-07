<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class MovieResource
 * @package App\Http\Resources
 *
 * @mixin \App\Models\Movie
 */
class MovieResource extends JsonResource
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
            'title'    => (string)$this->title,
            'slug'     => (string)$this->slug,
            'year'     => (int)$this->year,
            'runtime'  => (int)$this->runtime,
            'summary'  => (string)$this->summary,
            'poster'   => 'storage/' . $this->poster,
            'backdrop' => $this->backdrop,
            'cast'     => $this->whenLoaded('cast', CastResource::collection($this->cast)),
            'genres'   => $this->whenLoaded('genres', GenreResource::collection($this->genres)),
            'ratings'  => $this->whenLoaded('ratings', RatingResource::collection($this->ratings)),
        ];
    }
}
