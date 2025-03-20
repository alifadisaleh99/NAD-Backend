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
            'owner'              => new UserResource($this->whenLoaded('user')),
            'status'            => $this->status,
            'link'              => $this->link,
            'name'              => $this->name,
            'description'       => $this->description,
            'like'              =>$this->like,
            'deslike'           => $this->deslike,
            'good_with'         => $this->good_with,
            'bad_with'          => $this->bad_with,
            'photos'            => MediaResource::collection($this->whenLoaded('media')),
            'category'          => new CategoryResource($this->whenLoaded('category')),
            'animal_type'       => new AnimalTypeResource($this->whenLoaded('animal_type')),
            'animal_specie'     => new AnimalSpecieResource($this->whenLoaded('animal_specie')),
            'animal_breed'      => new AnimalBreedResource($this->whenLoaded('animal_breed')),
            'pet_marks'          => PetMarkResource::collection($this->whenLoaded('pet_marks')),
            'primary_color'     => new ColorResource($this->whenLoaded('primaryColor')),
            'primary_hex_color' => $this->primary_color,
            'secondary_color'   => new ColorResource($this->whenLoaded('secondaryColor')),
            'secondary_hex_color'  => $this->secondary_color,
           'tertiary_color'    => new ColorResource($this->whenLoaded('tertiaryColor')),
            'tertiary_hex_color'   => $this->tertiary_color,
            'age' => $this->age,
            'gender' => $this->gender,
            'size'  => $this->size,
            'birth_date' => $this->birth_date,
            'tags'            => TagResource::collection($this->whenLoaded('tags')),
            'created_at'   => $this->created_at,
            'created_by'   => new UserResource($this->whenLoaded('user_create')),
            'translations' => $this->translations,
        ];
    }
}
