<?php

namespace App\Http\Controllers;

use App\Http\Requests\GetRequest;
use App\Http\Resources\EntityResource;
use App\Models\Entity;
use App\Services\EntityService;
use Illuminate\Http\Request;

class EntityController extends Controller
{
    public $entityService;

    public function __construct(EntityService $entityService)
    {
        $this->entityService = $entityService;
    }
    /**
     * @OA\Get(
     * path="/entities",
     * description="Get all entities",
     * operationId="get_all_entities_for_user",
     * tags={"User - Entities"},
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
     *    name="branch_type_id",
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
        $entities = $this->entityService->getAllEntities($request);

        return EntityResource::collection($entities);
    }

    /**
     * @OA\Get(
     * path="/entities/{id}",
     * description="Get entity information.",
     *     @OA\Parameter(
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(type="string"),
     *      ),
     * operationId="show_entity_for_user",
     * tags={"User - Entities"},
     * @OA\Response(
     *    response=200,
     *    description="successful operation"
     * ),
     * )
     *)
     */
    public function show(Entity $entity)
    {
        $entity->load(['branches', 'branch_type']);

        return response()->json(new EntityResource($entity), 200);
    }
}
