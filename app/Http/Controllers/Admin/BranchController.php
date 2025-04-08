<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\BranchRequest;
use App\Http\Requests\GetRequest;
use App\Http\Resources\BranchResource;
use App\Models\Branch;
use App\Services\BranchService;

class BranchController extends Controller
{
    public $branchService;

    public function __construct(BranchService $branchService)
    {
        $this->middleware('auth:sanctum');
        $this->middleware('permission:entities.read|entities.write|entities.delete')->only('index', 'show');
        $this->middleware('permission:entities.write')->only('store', 'update');
        $this->middleware('permission:entities.delete')->only('destroy');

        $this->branchService = $branchService;
    }

    /**
     * @OA\Get(
     * path="/admin/branches",
     * description="Get all branches",
     * operationId="get_all_branches",
     * tags={"Admin - Branches"},
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
     *    name="entity_id",
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
        $branches = $this->branchService->getAllBranches($request);

        return BranchResource::collection($branches);
    }

    /**
     * @OA\Post(
     * path="/admin/branches",
     * description="Add new branch.",
     * tags={"Admin - Branches"},
     * security={{"bearer_token": {} }},
     *   @OA\RequestBody(
     *       required=true,
     *       @OA\MediaType(
     *           mediaType="multipart/form-data",
     *           @OA\Schema(
     *              required={"address", "entity_id"},
     *              @OA\Property(property="entity_id", type="integer"),
     *              @OA\Property(property="address", type="string"),
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
    public function store(BranchRequest $request)
    {
        $branch  = $this->branchService->create($request);

        return response()->json(new BranchResource($branch), 200);
    }

    /**
     * @OA\Get(
     * path="/admin/branches/{id}",
     * description="Get branch information.",
     *     @OA\Parameter(
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(type="string"),
     *      ),
     * operationId="show_branch",
     * tags={"Admin - Branches"},
     * security={{"bearer_token": {} }},
     * @OA\Response(
     *    response=200,
     *    description="successful operation"
     * ),
     * )
     *)
    */
    public function show(Branch $branch)
    {
        $branch->load('entity');

        return response()->json(new BranchResource($branch), 200);
    }

    /**
     * @OA\Post(
     * path="/admin/branches/{id}",
     * description="Edit branch.",
     *   @OA\Parameter(
     *     in="path",
     *     name="id",
     *     required=true,
     *     @OA\Schema(type="string"),
     *   ),
     *  tags={"Admin - Branches"},
     *  security={{"bearer_token": {} }},
     *   @OA\RequestBody(
     *       required=true,
     *       @OA\MediaType(
     *           mediaType="multipart/form-data",
     *           @OA\Schema(
     *              @OA\Property(property="address", type="string"),
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
    public function update(BranchRequest $request, Branch $branch)
    {
        $this->branchService->update($request, $branch);

        return response()->json(new BranchResource($branch), 200);
    }

    /**
     * @OA\Delete(
     * path="/admin/branches/{id}",
     * description="Delete entered Branch.",
     *     @OA\Parameter(
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(type="string"),
     *         @OA\Examples(example="int", value="1", summary="An int value."),
     *      ),
     * operationId="delete_branch",
     * tags={"Admin - Branches"},
     * security={{"bearer_token":{}}},
     * @OA\Response(
     *    response=204,
     *    description="successful operation"
     * ),
     * )
     *)
    */
    public function destroy(Branch $branch)
    {
        $branch->delete();

        return response()->json(null, 204);
    }
}

