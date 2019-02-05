<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class LibraryResource
 * @package App\Http\Resources
 *
 * @mixin \App\Models\Library
 */
class LibraryResource extends JsonResource
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
            'name' => (string)$this->name,
            'type' => (string)$this->type,
            'lang' => (string)$this->metadata_lang,
            'path' => (string)$this->path,
        ];
    }
}
