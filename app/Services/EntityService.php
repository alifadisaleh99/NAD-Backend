<?php

namespace App\Services;

use App\Models\Entity;
use Mosab\Translation\Models\Translation;

class EntityService
{
    public function getAllEntities($request)
    {
        $q =  Entity::query()->with(['branches', 'branch_type'])->latest();

        if ($request->branch_type_id)
            $q->where('branch_type_id', $request->branch_type_id);

        if ($request->q) {
            $entities_ids = Translation::where('translatable_type', Entity::class)
                ->where('attribute', 'name')
                ->where('value', 'LIKE', '%' . $request->q . '%')
                ->groupBy('translatable_id')
                ->pluck('translatable_id');

            $q->where(function ($query) use ($request, $entities_ids) {
                if (is_numeric($request->q))
                    $query->where('id', $request->q);

                $query->orWhereIn('id', $entities_ids);
            });
        }

        if ($request->with_paginate === '0')
            $entities = $q->get();
        else
            $entities = $q->paginate($request->per_page ?? 10);

        return $entities;
    }
}
