<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class RatingResource
 * @package App\Http\Resources
 *
 * @mixin \App\Models\Rating
 */
class RatingResource extends JsonResource
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
            'provider'   => $this->provider,
            'providerId' => $this->provider_id,
            'score'      => (float)$this->score,
        ];
    }
}
