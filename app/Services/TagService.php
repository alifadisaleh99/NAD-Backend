<?php

namespace App\Services;

use App\Models\Tag;
use Illuminate\Validation\ValidationException;

class TagService
{
    public function update($request)
    {
        if ($request->tags) {
            foreach ($request->tags as $tag) {
                if (isset($tag['number'])) {
                    $query = Tag::where('number', $tag['number']);

                    if ($request->deleted_tag_ids) {
                        $query->whereNotIn('id', $request->deleted_tag_ids);
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
                            'tag_type_id' => $tag['tag_type_id'],
                            'factory_number' => $tag['factory_number']?? null,
                            'number'  => $tag['number'],
                            'status'  => $tag['status'],
                            'animal_id'    => $request->animal_id,
                        ]);
                        $animal_tags->push($updated_tag);
                    }
                } else {
                    $created_tag = Tag::create([
                        'number' => $tag['number'],
                        'tag_type_id' => $tag['tag_type_id'],
                        'factory_number' => $tag['factory_number'] ?? null,
                        'status' => $tag['status'],
                        'animal_id' => $request->animal_id,
                    ]);
                    $animal_tags->push($created_tag);
                }
            }
        }

        return $animal_tags;
    }
}
