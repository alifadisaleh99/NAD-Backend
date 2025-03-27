<?php

namespace App\Http\Controllers;

use App\Http\Requests\GetRequest;
use App\Http\Resources\BranchTypeResource;
use App\Models\BranchType;
use Illuminate\Http\Request;
use Mosab\Translation\Models\Translation;

class BranchTypeController extends Controller
{
    /**
     * @OA\Get(
     * path="/branch-types",
     * description="Get all branch types",
     * operationId="get_all_branch_types_to_user",
     * tags={"User - Branch Types"},
     *   security={{"bearer_token": {} }},
     * @OA\Parameter(
     *     in="query",
     *     name="with_paginate",
     *     required=false,
     *     @OA\Schema(type="integer",enum={0, 1})
     *   ),
     * @OA\Parameter(
     *    in="query",
     *    name="per_page",
     *    required=false,
     *    @OA\Schema(type="integer"),
     * ),
     * @OA\Parameter(
     *    in="query",
     *    name="q",
     *    required=false,
     *    @OA\Schema(type="string"),
     * ),
     *   @OA\Response(
     *     response=200,
     *     description="Success",
     *  )
     *  )
     */
    public function index(GetRequest $request)
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

        return BranchTypeResource::collection($branch_types);
    }
    /**
     * @OA\Get(
     * path="/branch-types/{id}",
     * description="Get branch type information.",
     *     @OA\Parameter(
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(type="string"),
     *      ),
     * operationId="show_branch_type_to_user",
     * tags={"User - Branch Types"},
     * security={{"bearer_token": {} }},
     * @OA\Response(
     *    response=200,
     *    description="successful operation"
     * ),
     * )
     *)
     */
    public function show(BranchType $branch_type)
    {
        return response()->json(new BranchTypeResource($branch_type), 200);
    }
}
