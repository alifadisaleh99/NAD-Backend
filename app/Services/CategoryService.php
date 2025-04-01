<?php

namespace App\Services;

use App\Models\Category;
use Mosab\Translation\Models\Translation;

class CategoryService
{

    public function getAllCategories($request)
    {
        $q = Category::query()->with('animals')->latest();

        if ($request->q) {
            $categories_ids = Translation::where('translatable_type', Category::class)
                ->where('attribute', 'name')
                ->where('value', 'LIKE', '%' . $request->q . '%')
                ->groupBy('translatable_id')
                ->pluck('translatable_id');

            $q->where(function ($query) use ($request, $categories_ids) {
                if (is_numeric($request->q))
                    $query->where('id', $request->q);

                $query->orWhereIn('id', $categories_ids);
            });
        }

        if ($request->with_paginate === '0')
            $categories = $q->get();
        else
            $categories = $q->paginate($request->per_page ?? 10);

        return $categories;
    }
}
