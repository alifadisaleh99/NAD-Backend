<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AnimalTypeRequest;
use App\Http\Requests\GetRequest;
use App\Http\Resources\AnimalTypeResource;
use App\Models\AnimalType;
use App\Services\AnimalTypeService;

class AnimalTypeController extends Controller
{
    public  $animalTypeService;

    public function __construct(AnimalTypeService $animalTypeService)
    {
        $this->middleware('auth:sanctum');
        $this->middleware('permission:animalTypes.read|animalTypes.write|animalTypes.delete')->only('index', 'show');
        $this->middleware('permission:animalTypes.write')->only('store', 'update');
        $this->middleware('permission:animalTypes.delete')->only('destroy');

        $this->animalTypeService = $animalTypeService;
    }

    /**
     * @OA\Get(
     * path="/admin/animal-types",
     * description="Get all animal types",
     * operationId="get_all_animal_types",
     * tags={"Admin - Animal Types"},
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
     * @OA\Post(
     * path="/admin/animal-types",
     * description="Add new animal type.",
     * tags={"Admin - Animal Types"},
     * security={{"bearer_token": {} }},
     *   @OA\RequestBody(
     *       required=true,
     *       @OA\MediaType(
     *           mediaType="multipart/form-data",
     *           @OA\Schema(
     *              required={"name[ar]"},
     *              @OA\Property(property="category_id", type="integer"),
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
    public function store(AnimalTypeRequest $request)
    {
        $animal_type = $this->animalTypeService->create($request);

        return response()->json(new AnimalTypeResource($animal_type), 200);
    }

    /**
     * @OA\Get(
     * path="/admin/animal-types/{id}",
     * description="Get animal type information.",
     *     @OA\Parameter(
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(type="string"),
     *      ),
     * operationId="show_animal_type",
     * tags={"Admin - Animal Types"},
     * security={{"bearer_token": {} }},
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

    /**
     * @OA\Post(
     * path="/admin/animal-types/{id}",
     * description="Edit animal type.",
     *   @OA\Parameter(
     *     in="path",
     *     name="id",
     *     required=true,
     *     @OA\Schema(type="string"),
     *   ),
     *  tags={"Admin - Animal Types"},
     *  security={{"bearer_token": {} }},
     *   @OA\RequestBody(
     *       required=true,
     *       @OA\MediaType(
     *           mediaType="multipart/form-data",
     *           @OA\Schema(
     *              @OA\Property(property="category_id", type="integer"),
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
    public function update(AnimalTypeRequest $request, AnimalType $animal_type)
    {
        $this->animalTypeService->update($request, $animal_type);

        return response()->json(new AnimalTypeResource($animal_type), 200);
    }

    /**
     * @OA\Delete(
     * path="/admin/animal-types/{id}",
     * description="Delete entered animal type.",
     *     @OA\Parameter(
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(type="string"),
     *         @OA\Examples(example="int", value="1", summary="An int value."),
     *      ),
     * operationId="delete_animal_type",
     * tags={"Admin - Animal Types"},
     * security={{"bearer_token":{}}},
     * @OA\Response(
     *    response=204,
     *    description="successful operation"
     * ),
     * )
     *)
    */
    public function destroy(AnimalType $animal_type)
    {
        $animal_type->delete();

        return response()->json(null, 204);
    }
}
