<?php

namespace App\Services;

use App\Models\AnimalBreed;
use Mosab\Translation\Models\Translation;

class AnimalBreedService
{

    public function getAllAnimalBreeds($request)
    {
        $q = AnimalBreed::query()->with(['category', 'animal_type', 'animal_specie'])->latest();

        if($request->category_id)
           $q->where('category_id', $request->category_id);
        if($request->animal_type_id)
           $q->where('animal_type_id', $request->animal_type_id);
        if($request->animal_specie_id)
           $q->where('animal_specie_id', $request->animal_specie_id);

        if($request->q)
        {
            $animal_breeds_ids = Translation::where('translatable_type', AnimalBreed::class)
                                        ->where('attribute', 'name')
                                        ->where('value', 'LIKE', '%'.$request->q.'%')
                                        ->groupBy('translatable_id')
                                        ->pluck('translatable_id');

            $q->where(function($query) use ($request, $animal_breeds_ids) {
                if (is_numeric($request->q))
                    $query->where('id', $request->q);
        
                $query->orWhereIn('id', $animal_breeds_ids);
            });
        }

        if($request->with_paginate === '0')
            $animal_breeds = $q->get();
        else
            $animal_breeds = $q->paginate($request->per_page ?? 10);

        return $animal_breeds;
    }

    public function create($request)
    {
        $animal_breed = AnimalBreed::create([
            'category_id'       => $request->category_id,
            'animal_type_id'    => $request->animal_type_id,
            'animal_specie_id'  => $request->animal_specie_id,
            'name'              => $request->name,
        ]);

        return $animal_breed;
    }

    public function update($request, AnimalBreed $animal_breed)
    {
        $animal_breed->update([
            'category_id'       => $request->category_id,
            'animal_type_id'    => $request->animal_type_id,
            'animal_specie_id'  => $request->animal_specie_id,
            'name'              => $request->name,
        ]);
    }
}
