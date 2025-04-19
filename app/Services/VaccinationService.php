<?php

namespace App\Services;

use App\Models\Vaccination;

class VaccinationService
{
    public function getAllVaccinations($request)
    {
        $q = Vaccination::query()->with('animal')->latest();

        if($request->animal_id)
           $q->where('animal_id', $request->animal_id);
        
           if ($request->q) {
                if (is_numeric($request->q))
                      $q->where('id', $request->q);
                else
                    $q->where('name', 'LIKE', '%' . $request->q . '%');
            }

        if($request->with_paginate === '0')
          $vaccinations = $q->get();
        else
          $vaccinations = $q->paginate($request->per_page ?? 10);

        return $vaccinations;
    }
}