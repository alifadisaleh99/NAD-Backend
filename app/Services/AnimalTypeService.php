<?php

namespace App\Services;

use App\Models\AnimalType;
use Mosab\Translation\Models\Translation;

class AnimalTypeService
{
    public function getAllAnimalTypes($request)
    {
        $q = AnimalType::query()->with('category')->latest();

        if ($request->category_id)
            $q->where('category_id', $request->category_id);


        if ($request->q) {
            $animal_types_ids = Translation::where('translatable_type', AnimalType::class)
                ->where('attribute', 'name')
                ->where('value', 'LIKE', '%' . $request->q . '%')
                ->groupBy('translatable_id')
                ->pluck('translatable_id');

            $q->where(function ($query) use ($request, $animal_types_ids) {
                if (is_numeric($request->q))
                    $query->where('id', $request->q);

                $query->orWhereIn('id', $animal_types_ids);
            });
        }

        if ($request->with_paginate === '0')
            $animal_types = $q->get();
        else
            $animal_types = $q->paginate($request->per_page ?? 10);

        return $animal_types;
    }

    public function create($request)
    {
        $animal_type = AnimalType::create([
            'category_id'   => $request->category_id,
            'name'          => $request->name,
        ]);

        return $animal_type;
    }

    public function update($request, AnimalType $animal_type)
    {
        $animal_type->update([
            'category_id' => $request->category_id,
            'name'         => $request->name,
        ]);
    }
}
