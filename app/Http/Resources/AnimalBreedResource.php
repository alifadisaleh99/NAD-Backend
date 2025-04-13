<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AnimalBreedResource extends JsonResource
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
            'animal_specie'     => new AnimalSpecieResource($this->whenLoaded('animal_specie')),
            'translations'      => $this->translations,
        ];
    }
}
