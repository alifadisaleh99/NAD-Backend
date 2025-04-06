<?php

namespace App\Services;

use App\Models\Category;
use Mosab\Translation\Models\Translation;
use Illuminate\Validation\ValidationException;

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

    public function create($request)
    {
        $image = upload_file($request->image, 'categories', 'category');
     
        $category = Category::create([
            'name'          => $request->name,
            'description'   => $request->description,
            'image'         => $image,
        ]);
        
        return $category;
    }

    public function update($request, Category $category)
    {
        $image = null;
        if ($request->image) {
            if ($request->image == $category->image) {
                $image = $category->image;
            } else {
                if (!is_file($request->image))
                    throw ValidationException::withMessages(['image' => __('error_messages.Image should be a file')]);
                delete_file_if_exist($category->image);
                $image = upload_file($request->image, 'categories', 'category');
            }
        }

        $category->update([
            'name'          => $request->name,
            'description'   => $request->description,
            'image'         => $image,
        ]);
    }
}
