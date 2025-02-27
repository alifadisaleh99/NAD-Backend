<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AnimalStatusResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                    => $this->id,
            'status'                => $this->status,
            'description'           => $this->description,
            'animal'                => new AnimalResource($this->whenLoaded('animal')),
            'user_created'          => new UserResource($this->whenLoaded('user')),
            'translations'          => $this->translations,
            'created_at'            => $this->created_at,
            'updated_at'            => $this->created_at,
        ];
    }
}
