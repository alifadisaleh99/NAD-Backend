<?php

namespace App\Services;

use App\Models\Tag;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class TagService
{
    public function update($request)
    {
        if ($request->tags) {
            foreach ($request->tags as $tag) {
                if (isset($tag['factory_number'])) {
                    $query = Tag::where('factory_number', $tag['factory_number']);

                    if ($request->deleted_tag_ids) {
                        $query->whereNotIn('id', $request->deleted_tag_ids);
                    }
                    if (isset($tag['id'])) {
                        $query->where('id', '!=', $tag['id']);
                    }

                    if ($query->exists()) {
                        throw ValidationException::withMessages([
                            'factory_number' => __('error_messages.factory_number_used', ['Fnumber' => $tag['factory_number']])
                        ]);
                    }
                }
            }
        }

        if ($request->deleted_tag_ids) {
            Tag::whereIn('id', $request->deleted_tag_ids)->delete();
        }

        $animal_tags = collect();

        if ($request->tags) {
            foreach ($request->tags as $tag) {
                if (isset($tag['id'])) {
                    $updated_tag = Tag::find($tag['id']);
                    if ($updated_tag) {
                        $updated_tag->update([
                            'tag_type_id' => $tag['tag_type_id'] ?? $updated_tag->tag_type_id,
                            'factory_number' => $tag['factory_number'] ?? $updated_tag->factory_number,
                            'number'  => $tag['number'] ?? $updated_tag->number,
                            'status'  => isset($tag['status']) ? $tag['status'] : $updated_tag->status,
                            'animal_id'    => $request->animal_id,
                        ]);
                        $animal_tags->push($updated_tag);
                    }
                } else {
                    $created_tag = Tag::create([
                        'number' => Str::random(15),
                        'tag_type_id' => $tag['tag_type_id'] ?? null,
                        'factory_number' => $tag['factory_number'] ?? null,
                        'status' => isset($tag['status']) ? $tag['status'] : 1,
                        'animal_id' => $request->animal_id,
                    ]);
                    $animal_tags->push($created_tag);
                }
            }
        }

        return $animal_tags;
    }
}
