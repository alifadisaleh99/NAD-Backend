<?php

namespace App\Services;

use App\Models\Branch;
use Mosab\Translation\Models\Translation;

class BranchService
{
    public function getAllBranches($request)
    {
        $q =  Branch::with('entity')->latest();

        if ($request->entity_id)
            $q->where('entity_id', $request->entity_id);

        if ($request->q) {
            $branches_ids = Translation::where('translatable_type', Branch::class)
                ->where('attribute', 'name')
                ->where('value', 'LIKE', '%' . $request->q . '%')
                ->groupBy('translatable_id')
                ->pluck('translatable_id');

            $q->where(function ($query) use ($request, $branches_ids) {
                if (is_numeric($request->q))
                    $query->where('id', $request->q);

                $query->orWhereIn('id', $branches_ids);
            });
        }

        if ($request->with_paginate === '0')
            $branches = $q->get();
        else
            $branches = $q->paginate($request->per_page ?? 10);

        return $branches;
    }
}
