<?php

namespace App\Http\Resources;

use App\Models\Animal;
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
            'owner'             => new UserResource($this->whenLoaded('user')),
            'branch'            => new BranchResource($this->whenLoaded('branch')),
            'status'            => $this->status,
            'link'              => $this->link,
            'name'              => $this->name,
            'description'       => $this->description,
            'like'              =>$this->like,
            'deslike'           => $this->deslike,
            'good_with'         => $this->good_with,
            'bad_with'          => $this->bad_with,
            'sensitivities'     => AnimalSensitivityResource::collection($this->whenLoaded('sensitivities')),
            'photos'            => MediaResource::collection($this->whenLoaded('media')),
            'category'          => new CategoryResource($this->whenLoaded('category')),
            'animal_specie'     => new AnimalSpecieResource($this->whenLoaded('animal_specie')),
            'animal_breed'      => new AnimalBreedResource($this->whenLoaded('animal_breed')),
            'pet_marks'          => PetMarkResource::collection($this->whenLoaded('pet_marks')),
            'primary_color'     => new ColorResource($this->whenLoaded('primary_color')),
            'secondary_color'   => new ColorResource($this->whenLoaded('secondary_color')),
            'tertiary_color'    => new ColorResource($this->whenLoaded('tertiary_color')),
            'attachments'       => AttachmentResource::collection($this->whenLoaded('attachments')),
            'vaccinations'       => VaccinationResource::collection($this->whenLoaded('vaccinations')),
            'age' => $this->age,
            'weight' => $this->weight,
            'gender' => $this->gender,
            'size'  => $this->size,
            'birth_date' => $this->birth_date,
            'age_now' => $this->calculateAge($this->birth_date),
            'tags'            => TagResource::collection($this->whenLoaded('tags')),
            'created_at'   => $this->created_at,
            'created_by'   => new UserResource($this->whenLoaded('user_create')),
            'uaid'         => $this->uaid,
            'pet_status'   => $this->pet_status,
            'latest_lost_report' =>  $this->pet_status == 'lost' ? new LostReportResource($this->whenLoaded('latest_lost_report')) : null,
            'digital_link' => $this->digital_link, 
            'generate_public' => $this->generate_public, 
            'ownership_date' => $this->ownership_date,
            'is_previous' => $this->user_id == auth()->id() ? 0 : 1,
            'translations' => $this->translations,
        ];
    }
}
