<?php

namespace App\Http\Controllers;

use App\Http\Requests\GetRequest;
use App\Http\Resources\AnimalTypeResource;
use App\Models\AnimalType;
use App\Services\AnimalTypeService;

class AnimalTypeController extends Controller
{
    public  $animalTypeService;

    public function __construct(AnimalTypeService $animalTypeService)
    {
        $this->animalTypeService = $animalTypeService;
    }

    /**
     * @OA\Get(
     * path="/animal-types",
     * description="Get all animal types",
     * operationId="get_all_animal_types_for_user",
     * tags={"User - Animal Types"},
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
     *    name="category_id",
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
        $animal_types = $this->animalTypeService->getAllAnimalTypes($request);

        return AnimalTypeResource::collection($animal_types);
    }
    /**
     * @OA\Get(
     * path="/animal-types/{id}",
     * description="Get animal type information.",
     *     @OA\Parameter(
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(type="string"),
     *      ),
     * operationId="show_animal_type_for_user",
     * tags={"User - Animal Types"},
     * @OA\Response(
     *    response=200,
     *    description="successful operation"
     * ),
     * )
     *)
     */
    public function show(AnimalType $animal_type)
    {
        $animal_type->load(['category']);

        return response()->json(new AnimalTypeResource($animal_type), 200);
    }
}
