<?php

namespace App\Services;

use App\Models\BranchType;
use Mosab\Translation\Models\Translation;

class BranchTypeService
{

    public function getAllBranchTypes($request)
    {
        $q = BranchType::query()->latest();
    
        if ($request->q) {
            $branch_types_ids = Translation::where('translatable_type', BranchType::class)
                ->where('attribute', 'name')
                ->where('value', 'LIKE', '%' . $request->q . '%')
                ->groupBy('translatable_id')
                ->pluck('translatable_id');

            $q->where(function ($query) use ($request, $branch_types_ids) {
                if (is_numeric($request->q))
                    $query->where('id', $request->q);

                $query->orWhereIn('id', $branch_types_ids);
            });
        }

        if ($request->with_paginate === '0')
            $branch_types = $q->get();
        else
            $branch_types = $q->paginate($request->per_page ?? 10);

        return $branch_types;
    }

    public function create($request)
    {
        $branch_type = BranchType::create([
            'name'          => $request->name,
        ]);

        return $branch_type;
    }

    public function update($request, BranchType $branch_type)
    {
        $branch_type->update([
            'name'         => $request->name,
        ]);
    }
}