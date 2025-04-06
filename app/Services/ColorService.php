<?php

namespace App\Services;

use App\Models\Color;
use Mosab\Translation\Models\Translation;

class ColorService
{

    public function getAllColors($request)
    {
        $q = Color::query()->latest();

        if ($request->q) {
            $colors_ids = Translation::where('translatable_type', Color::class)
                ->where('attribute', 'name')
                ->where('value', 'LIKE', '%' . $request->q . '%')
                ->groupBy('translatable_id')
                ->pluck('translatable_id');

            $q->where(function ($query) use ($request, $colors_ids) {
                if (is_numeric($request->q))
                    $query->where('id', $request->q);

                $query->orWhereIn('id', $colors_ids);
            });
        }

        if ($request->with_paginate === '0')
            $colors = $q->get();
        else
            $colors = $q->paginate($request->per_page ?? 10);

        return $colors;
    }

    public function create($request)
    {
        $color = Color::create([
            'name'          => $request->name,
        ]);

        return $color;
    }

    public function update($request, Color $color)
    {
        $color->update([
            'name'          => $request->name,
        ]);
    }
}
