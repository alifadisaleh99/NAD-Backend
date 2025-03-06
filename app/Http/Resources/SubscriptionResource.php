<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionResource extends JsonResource
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
            'user_name'             => $this->user_name,
            'plan_price'            => $this->plan_price,
            'plane'                 => $this->plan,
            'subscription_date'     => $this->created_at,
            'is_active'             => $this->is_active,
        ];

    }
}
