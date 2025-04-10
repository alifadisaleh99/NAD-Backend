<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AnimalSpecieResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'name'              => $this->name, 
            'image'             => $this->image,
            'category'          => new CategoryResource($this->whenLoaded('category')),
            'animal_type'       => new AnimalTypeResource($this->whenLoaded('animal_type')),
            'translations'      => $this->translations,
        ];
    }
}
