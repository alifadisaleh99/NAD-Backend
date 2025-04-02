<?php

namespace App\Services;

use App\Models\PetMark;
use Mosab\Translation\Models\Translation;

class PetMarkService
{
    public function getAllPetMarks($request)
    {
        $q = PetMark::query()->latest();

        if($request->q)
        {
            $pet_marks_ids = Translation::where('translatable_type', PetMark::class)
                                        ->where('attribute', 'name')
                                        ->where('value', 'LIKE', '%'.$request->q.'%')
                                        ->groupBy('translatable_id')
                                        ->pluck('translatable_id');

            $q->where(function($query) use ($request, $pet_marks_ids) {
                if (is_numeric($request->q))
                    $query->where('id', $request->q);
        
                $query->orWhereIn('id', $pet_marks_ids);
            });
        }    

        if($request->with_paginate === '0')
          $pet_marks = $q->get();
        else
          $pet_marks = $q->paginate($request->per_page ?? 10);

        return $pet_marks;
    }
}
