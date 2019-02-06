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
            'start_year' => $this->start_year,
            'end_year'   => $this->end_year,
            'summary'    => $this->summary,
            'status'     => $this->status,
            'thetvdbId'  => $this->thetvdb_id,
        ];
    }
}
