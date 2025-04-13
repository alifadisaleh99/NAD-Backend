<?php

namespace App\Services;

use App\Models\AnimalBreed;
use Mosab\Translation\Models\Translation;
use Illuminate\Validation\ValidationException;

class AnimalBreedService
{

    public function getAllAnimalBreeds($request)
    {
        $q = AnimalBreed::query()->with(['category', 'animal_specie'])->latest();

        if($request->category_id)
           $q->where('category_id', $request->category_id);
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
        $image = null;
        if ($request->image)
           $image = upload_file($request->image, 'animalBreeds', 'animalBreed');

        $animal_breed = AnimalBreed::create([
            'category_id'       => $request->category_id,
            'animal_specie_id'  => $request->animal_specie_id,
            'name'              => $request->name,
            'image'             => $image,
        ]);

        return $animal_breed;
    }

    public function update($request, AnimalBreed $animal_breed)
    {
        $image = null;
        if ($request->image) {
            if ($request->image == $animal_breed->image) {
                $image = $animal_breed->image;
            } else {
                if (!is_file($request->image))
                    throw ValidationException::withMessages(['image' => __('error_messages.Image should be a file')]);
                delete_file_if_exist($animal_breed->image);
                $image = upload_file($request->image, 'animalBreeds', 'animalBreed');
            }
        }

        $animal_breed->update([
            'category_id'       => $request->category_id,
            'animal_specie_id'  => $request->animal_specie_id,
            'name'              => $request->name,
            'image'             => $image,
        ]);
    }
}
