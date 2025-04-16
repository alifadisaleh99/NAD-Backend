<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AttachmentResource extends JsonResource
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
            'aniaml_id' => $this->animal_id,
            'name' => $this->name,
            'source' => $this->source,
            'attachment_date' => $this->attachment_date,
            'file' => $this->file,
        ];
    }
}
