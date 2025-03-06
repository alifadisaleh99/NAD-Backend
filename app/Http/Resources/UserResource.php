<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $subscription = $this->subscriptions()->with('plan')->where('is_active',1)->first();

        return [
            'id'                => $this->id, 
            'nationality'       => new CountryResource($this->whenLoaded('nationality')), 
            'name'              => $this->name, 
            'email'             => $this->email, 
            'phone_country'     => new CountryResource($this->whenLoaded('phone_country')), 
            'entity'            => new EntityResource($this->whenLoaded('entity')), 
            'branch'            => new BranchResource($this->whenLoaded('branch')), 
            'phone'             => $this->phone, 
            'image'             => $this->image, 
            'summary'           => $this->summary, 
            'status'            => $this->status,
            'role'              => $this->roleModel(),
            'plan'              =>  new PlanResource($subscription->plan??null),
        ];
    }
}
