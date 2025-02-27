<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PlanResource extends JsonResource
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
            'price'                 => $this->price,
            'addition_count'        => $this->addition_count,
            'status'                => $this->status,
            'image'                 => $this->image,
        ];
    }
}
