<?php

namespace App\Services;

use App\Models\Animal;
use App\Models\Tag;
use Illuminate\Validation\ValidationException;

class TagService
{
    public function update(Animal $animal, $tags, $deleted_tag_ids)
    {
        if (!empty($tags)){
            foreach ($tags as $tag) {
                if (isset($tag['number'])) {
                    $query = Tag::where('number', $tag['number']);

                    if (!empty($deleted_tag_ids)) {
                        $query->whereNotIn('id', $deleted_tag_ids);
                    }
                    if (isset($tag['id'])) {
                        $query->where('id', '!=', $tag['id']);
                    }

                    if ($query->exists()) {
                        throw ValidationException::withMessages([
                            'number' => __('error_messages.number_used', ['number' => $tag['number']])
                        ]);
                    }
                }
            }
        }

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
