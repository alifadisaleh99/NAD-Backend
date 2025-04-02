<?php

namespace App\Http\Controllers;

use App\Http\Requests\GetRequest;
use App\Http\Resources\BranchResource;
use App\Models\Branch;
use App\Services\BranchService;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    public $branchService;

    public function __construct(BranchService $branchService)
    {
        $this->branchService = $branchService;
    }

    /**
     * @OA\Get(
     * path="/branches",
     * description="Get all branches",
     * operationId="get_all_branches_for_user",
     * tags={"User - Branches"},
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
     * @OA\Get(
     * path="/branches/{id}",
     * description="Get branch information.",
     *     @OA\Parameter(
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(type="string"),
     *      ),
     * operationId="show_branch_for_user",
     * tags={"User - Branches"},
     * @OA\Response(
     *    response=200,
     *    description="successful operation"
     * ),
     * )
     *)
    */
    public function show(Branch $branch)
    {       $branch->load('entity');
        
        return response()->json(new BranchResource($branch), 200);
    }
}
