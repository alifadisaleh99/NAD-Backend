<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LogoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'mobile_light_logo' => $this->mobile_light_logo,
            'mobile_dark_logo' =>  $this->mobile_dark_logo,
            'light_logo' =>  $this->light_logo,
            'dark_logo' =>  $this->dark_logo,
            ];

    }
}
