<?php

namespace App\Services;

use App\Models\AnimalSpecie;
use Mosab\Translation\Models\Translation;
use Illuminate\Validation\ValidationException;

class AnimalSpecieService
{
    public function getAllAnimalSpecies($request)
    {
        $q = AnimalSpecie::query()->with(['category', 'animal_type'])->latest();

        if ($request->category_id)
            $q->where('category_id', $request->category_id);
        if ($request->animal_type_id)
            $q->where('animal_type_id', $request->animal_type_id);

        if ($request->q) {
            $animal_species_ids = Translation::where('translatable_type', AnimalSpecie::class)
                ->where('attribute', 'name')
                ->where('value', 'LIKE', '%' . $request->q . '%')
                ->groupBy('translatable_id')
                ->pluck('translatable_id');

            $q->where(function ($query) use ($request, $animal_species_ids) {
                if (is_numeric($request->q))
                    $query->where('id', $request->q);

                $query->orWhereIn('id', $animal_species_ids);
            });
        }

        if ($request->with_paginate === '0')
            $animal_species = $q->get();
        else
            $animal_species = $q->paginate($request->per_page ?? 10);

        return $animal_species;
    }

    public function create($request)
    {
        $image = null;
        if ($request->image)
           $image = upload_file($request->image, 'animalSpecies', 'animalSpecie');

        $animal_specie = AnimalSpecie::create([
            'category_id'       => $request->category_id,
            'animal_type_id'    => $request->animal_type_id,
            'name'              => $request->name,
            'image'             => $image,
        ]);

        return $animal_specie;
    }

    public function update($request, AnimalSpecie $animal_specie)
    {
        $image = null;
        if ($request->image) {
            if ($request->image == $animal_specie->image) {
                $image = $animal_specie->image;
            } else {
                if (!is_file($request->image))
                    throw ValidationException::withMessages(['image' => __('error_messages.Image should be a file')]);
                delete_file_if_exist($animal_specie->image);
                $image = upload_file($request->image, 'animalSpecies', 'animalSpecie');
            }
        }

        $animal_specie->update([
            'category_id'       => $request->category_id,
            'animal_type_id'    => $request->animal_type_id,
            'name'              => $request->name,
            'image'             => $image,
        ]);
    }
}
