<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class ShowResource
 * @package App\Http\Resources
 *
 * @mixin \App\Models\Show
 */
class ShowResource extends JsonResource
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
            'title'      => $this->title,
            'slug'       => $this->slug,
            'poster'     => $this->poster,
            'startYear' => $this->start_year,
            'endYear'   => $this->end_year,
            'summary'    => $this->summary,
            'status'     => $this->status,
            'thetvdbId'  => $this->thetvdb_id,
            'seasons'    => $this->whenLoaded('seasons', SeasonResource::collection($this->seasons)),
        ];
    }
}
