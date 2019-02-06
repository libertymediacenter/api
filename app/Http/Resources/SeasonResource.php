<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class SeasonResource
 * @package App\Http\Resources
 *
 * @mixin \App\Models\Season
 */
class SeasonResource extends JsonResource
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
            'season'   => $this->season,
            'poster'   => $this->poster,
            'episodes' => $this->whenLoaded('episodes', EpisodeResource::collection($this->episodes)),
        ];
    }
}
