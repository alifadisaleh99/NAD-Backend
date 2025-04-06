<?php

namespace App\Services;

use App\Models\Branch;
use App\Models\Entity;
use Mosab\Translation\Models\Translation;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;


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

    public function create($request)
    {
        $entity = Entity::find($request->entity_id);

        if($entity->used_branches == $entity->allowed_branches)
            throw new BadRequestHttpException(__('error_messages.branches_limit_reached'));
     
        $branch = Branch::create([
            'entity_id'         => $request->entity_id,
            'address'           => $request->address,
        ]);

        $entity->used_branches = $entity->used_branches + 1;
        $entity->save();

        return $branch;
    }

    public function update($request, Branch $branch)
    {
        $branch->update([
            'address'           => $request->address,
        ]);
    }
}
