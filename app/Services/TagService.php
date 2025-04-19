<?php

namespace App\Services;

use App\Models\Animal;
use App\Models\Tag;
use Illuminate\Validation\ValidationException;

class TagService
{
    public function update(Animal $animal, $tags, $deleted_tag_ids)
    {
        if (!empty($deleted_tag_ids)) {
            $animal->tags()->whereIn('id', $deleted_tag_ids)->delete();
        }

        if (!empty($tags)) {
            foreach ($tags as $tag) {
                if (isset($tag['id'])) {
                    $animal_tag = $animal->tags()->find($tag['id']);
                    if ($animal_tag) {
                        $animal_tag->update([
                            'tag_type_id' => $tag['tag_type_id'],
                            'factory_number' => $tag['factory_number']?? null,
                            'number'  => $tag['number'],
                            'status'  => $tag['status'],
                        ]);
                    }
                } else {
                    $animal->tags()->create([
                        'number' => $tag['number'],
                        'tag_type_id' => $tag['tag_type_id'],
                        'factory_number' => $tag['factory_number'] ?? null,
                        'status' => $tag['status'],
                    ]);
                }
            }
        }
    }
}
