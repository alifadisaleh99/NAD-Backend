<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TagResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                     => $this->id,
            'tag_type'               => new TagTypeResource($this->tag_type),
            'animal_id'              => $this->animal_id,
            'number'                 => $this->number,
            'factory_number'         => $this->factory_number,
            'status'                 => $this->status,
        ];
    }
}
