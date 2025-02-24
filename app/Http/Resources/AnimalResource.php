<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AnimalResource extends JsonResource
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
            'owner_type'        => $this->owner_type,
            'user'              => new UserResource($this->whenLoaded('user')),
            'entity'            => new EntityResource($this->whenLoaded('entity')),
            'branch'            => new BranchResource($this->whenLoaded('branch')),
            'status'            => $this->status,
            'link'              => $this->link,
            'name'              => $this->name,
            'description'       => $this->description,
            'photos'            => MediaResource::collection($this->whenLoaded('media')),
            'category'          => new CategoryResource($this->whenLoaded('category')),
            'animal_type'       => new AnimalTypeResource($this->whenLoaded('animal_type')),
            'animal_specie'     => new AnimalSpecieResource($this->whenLoaded('animal_specie')),
            'animal_breed'      => new AnimalBreedResource($this->whenLoaded('animal_breed')),
            'primary_color'     => new ColorResource($this->whenLoaded('primaryColor')),
            'primary_hex_color' => $this->primary_color,
            'secondary_color'   => new ColorResource($this->whenLoaded('secondaryColor')),
            'secondary_hex_color'  => $this->secondary_color,
           'tertiary_color'    => new ColorResource($this->whenLoaded('tertiaryColor')),
            'tertiary_hex_color'   => $this->tertiary_color,
            'age' => $this->age,
            'gender' => $this->gender,
            'size'  => $this->size,
            'translations' => $this->translations,
        ];
    }
}
