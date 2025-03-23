<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\BranchTypeRequest;
use App\Http\Requests\GetRequest;
use App\Http\Resources\BranchTypeResource;
use App\Models\BranchType;
use Mosab\Translation\Models\Translation;

class BranchTypeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->middleware('permission:branchTypes.read|branchTypes.write|branchTypes.delete')->only('index', 'show');
        $this->middleware('permission:branchTypes.write')->only('store', 'update');
        $this->middleware('permission:branchTypes.delete')->only('destroy');

    }

    /**
     * @OA\Get(
     * path="/admin/branch-types",
     * description="Get all branch types",
     * operationId="get_all_branch_types",
     * tags={"Admin - Branch Types"},
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
     * @OA\Post(
     * path="/admin/branch-types",
     * description="Add new branch type.",
     * tags={"Admin - Branch Types"},
     * security={{"bearer_token": {} }},
     *   @OA\RequestBody(
     *       required=true,
     *       @OA\MediaType(
     *           mediaType="multipart/form-data",
     *           @OA\Schema(
     *              required={"name[ar]"},
     *              @OA\Property(property="name[en]", type="string"),
     *              @OA\Property(property="name[ar]", type="string"),
     *          )
     *       )
     *   ),
     * @OA\Response(
     *    response=200,
     *    description="successful operation",
     *     ),
     * )
     * )
    */
    public function store(BranchTypeRequest $request)
    {     
        $branch_type = BranchType::create([
            'name'          => $request->name,
        ]);

        return response()->json(new BranchTypeResource($branch_type), 200);
    }

    /**
     * @OA\Get(
     * path="/admin/branch-types/{id}",
     * description="Get branch type information.",
     *     @OA\Parameter(
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(type="string"),
     *      ),
     * operationId="show_branch_type",
     * tags={"Admin - Branch Types"},
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

    /**
     * @OA\Post(
     * path="/admin/branch-types/{id}",
     * description="Edit branch type.",
     *   @OA\Parameter(
     *     in="path",
     *     name="id",
     *     required=true,
     *     @OA\Schema(type="string"),
     *   ),
     *  tags={"Admin - Branch Types"},
     *  security={{"bearer_token": {} }},
     *   @OA\RequestBody(
     *       required=true,
     *       @OA\MediaType(
     *           mediaType="multipart/form-data",
     *           @OA\Schema(
     *              @OA\Property(property="name[en]", type="string"),
     *              @OA\Property(property="name[ar]", type="string"),
     *              @OA\Property(property="_method", type="string", format="string", example="PUT"),
     *           )
     *       )
     *   ),
     * @OA\Response(
     *         response="200",
     *         description="successful operation",
     *     ),
     * )
     * )
    */
    public function update(BranchTypeRequest $request, BranchType $branch_type)
    {
        $branch_type->update([
            'name'         => $request->name,
        ]);

        return response()->json(new BranchTypeResource($branch_type), 200);
    }

    /**
     * @OA\Delete(
     * path="/admin/branch-types/{id}",
     * description="Delete entered branch type.",
     *     @OA\Parameter(
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(type="string"),
     *         @OA\Examples(example="int", value="1", summary="An int value."),
     *      ),
     * operationId="delete_branch_type",
     * tags={"Admin - Branch Types"},
     * security={{"bearer_token":{}}},
     * @OA\Response(
     *    response=204,
     *    description="successful operation"
     * ),
     * )
     *)
    */
    public function destroy(BranchType $branch_type)
    {
        $branch_type->translations()->delete();
        $branch_type->delete();
        return response()->json(null, 204);
    }
}
