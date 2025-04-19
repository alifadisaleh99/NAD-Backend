<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VaccinationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'animal' => new AnimalResource($this->whenLoaded('animal')),
            'name' => $this->name,
            'vaccination_date' => $this->vaccination_date,
            'duration' => $this->duration,
            'is_expired' => $this->is_expired,
        ];
    }
}
