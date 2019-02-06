<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class EpisodeResource
 * @package App\Http\Resources
 *
 * @mixin \App\Models\Episode
 */
class EpisodeResource extends JsonResource
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
            'id' => $this->id,
            'title'     => $this->title,
            'summary'   => $this->summary,
            'runtime'   => $this->runtime,
            'poster'    => $this->poster,
            'thetvdbId' => $this->thetvdb_id,
            'imdbId'    => $this->imdb_id,
        ];
    }
}
