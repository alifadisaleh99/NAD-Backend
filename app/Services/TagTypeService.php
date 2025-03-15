<?php

namespace App\Services;

use App\Models\Tag;
use App\Models\TagType;
use Illuminate\Validation\ValidationException;
use Mosab\Translation\Models\Translation;

class TagTypeService
{

    public function getAllTagTypes($request)
    {
        $q = TagType::query()->latest();

        if ($request->q) {
            $tag_types_ids = Translation::where('translatable_type', TagType::class)
                ->where('attribute', 'name')
                ->where('value', 'LIKE', '%' . $request->q . '%')
                ->groupBy('translatable_id')
                ->pluck('translatable_id');

            $q->where(function ($query) use ($request, $tag_types_ids) {
                if (is_numeric($request->q))
                    $query->where('id', $request->q);

                $query->orWhereIn('id', $tag_types_ids);
            });
        }

        if ($request->with_paginate === '0')
            $tag_types = $q->get();
        else
            $tag_types = $q->paginate($request->per_page ?? 10);

        return $tag_types;
    }

    public function create($request)
    {
        $icon = null;

        if($request->icon){
            $icon = upload_file($request->icon, 'tagTypes', 'tagType');
        }
        $tag_type = TagType::create([
            'name'   => $request->name,
            'icon'   => $icon,
        ]);

        return $tag_type;
    }


    public function update($request, TagType $tag_type)
    {
        $icon = null;
        if ($request->icon) {
            if ($request->icon == $tag_type->icon) {
                $icon = $tag_type->icon;
            } else {
                if (!is_file($request->icon))
                    throw ValidationException::withMessages(['icon' => __('error_messages.Icon should be a file')]);
                delete_file_if_exist($tag_type->icon);
                $icon = upload_file($request->icon, 'tagTypes', 'tagType');
            }
        }

        $tag_type->update([
            'name'          => $request->name,
            'icon'         => $icon,
        ]);

        return $tag_type;
    }

    public function delete(TagType $tag_type)
    {
        delete_file_if_exist($tag_type->icon);
        $tag_type->delete();
    }
}
