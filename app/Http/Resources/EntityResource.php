<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EntityResource extends JsonResource
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
            'address'           => $this->address,
            'email'             => $this->email,
            'contact_number'    => $this->contact_number,
            'founding_date'     => $this->founding_date,
            'price_per_pet'     => $this->price_per_pet,
            'allowed_branches'  => $this->allowed_branches,
            'allowed_users'     => $this->allowed_users,
            'used_users'        => $this->used_users,
            'used_branches'     => $this->used_branches,
            'branches'          => BranchResource::collection($this->whenLoaded('branches')),
            'image'             => $this->image,
            'translations'      => $this->translations,
        ];
    }
}
