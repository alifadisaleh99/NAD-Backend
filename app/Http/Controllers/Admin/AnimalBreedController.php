<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AnimalBreedRequest;
use App\Http\Requests\GetRequest;
use App\Http\Resources\AnimalBreedResource;
use App\Models\AnimalBreed;
use App\Services\AnimalBreedService;

class AnimalBreedController extends Controller
{
    public $animalBreedService;

    public function __construct(AnimalBreedService $animalBreedService)
    {
        $this->middleware('auth:sanctum');
        $this->middleware('permission:animalBreeds.read|animalBreeds.write|animalBreeds.delete')->only('index', 'show');
        $this->middleware('permission:animalBreeds.write')->only('store', 'update');
        $this->middleware('permission:animalBreeds.delete')->only('destroy');

        $this->animalBreedService = $animalBreedService;
    }

    /**
     * @OA\Get(
     * path="/admin/animal-breeds",
     * description="Get all animal breeds",
     * operationId="get_all_animal_breeds",
     * tags={"Admin - Animal Breeds"},
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
     *    name="animal_specie_id",
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
        $animal_breeds = $this->animalBreedService->getAllAnimalBreeds($request);

        return AnimalBreedResource::collection($animal_breeds);
    }

    /**
     * @OA\Post(
     * path="/admin/animal-breeds",
     * description="Add new animal breed.",
     * tags={"Admin - Animal Breeds"},
     * security={{"bearer_token": {} }},
     *   @OA\RequestBody(
     *       required=true,
     *       @OA\MediaType(
     *           mediaType="multipart/form-data",
     *           @OA\Schema(
     *              required={"name[ar]", "category_id", "animal_specie_id"},
     *              @OA\Property(property="category_id", type="integer"),
     *              @OA\Property(property="animal_specie_id", type="integer"),
     *              @OA\Property(property="name[en]", type="string"),
     *              @OA\Property(property="name[ar]", type="string"),
     *              @OA\Property(property="image", type="file"),
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
    public function store(AnimalBreedRequest $request)
    {
        $animal_breed = $this->animalBreedService->create($request);

        return response()->json(new AnimalBreedResource($animal_breed), 200);
    }

    /**
     * @OA\Get(
     * path="/admin/animal-breeds/{id}",
     * description="Get animal breed information.",
     *     @OA\Parameter(
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(type="string"),
     *      ),
     * operationId="show_animal_breed",
     * tags={"Admin - Animal Breeds"},
     * security={{"bearer_token": {} }},
     * @OA\Response(
     *    response=200,
     *    description="successful operation"
     * ),
     * )
     *)
    */
    public function show(AnimalBreed $animal_breed)
    {
        $animal_breed->load(['category', 'animal_specie']);

        return response()->json(new AnimalBreedResource($animal_breed), 200);
    }

    /**
     * @OA\Post(
     * path="/admin/animal-breeds/{id}",
     * description="Edit animal breed.",
     *   @OA\Parameter(
     *     in="path",
     *     name="id",
     *     required=true,
     *     @OA\Schema(type="string"),
     *   ),
     *  tags={"Admin - Animal Breeds"},
     *  security={{"bearer_token": {} }},
     *   @OA\RequestBody(
     *       required=true,
     *       @OA\MediaType(
     *           mediaType="multipart/form-data",
     *           @OA\Schema(
     *              @OA\Property(property="category_id", type="integer"),
     *              @OA\Property(property="animal_specie_id", type="integer"),
     *              @OA\Property(property="name[en]", type="string"),
     *              @OA\Property(property="name[ar]", type="string"),
     *              @OA\Property(property="image", type="file"),
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
    public function update(AnimalBreedRequest $request, AnimalBreed $animal_breed)
    {
        $this->animalBreedService->update($request, $animal_breed);

        return response()->json(new AnimalBreedResource($animal_breed), 200);
    }

    /**
     * @OA\Delete(
     * path="/admin/animal-breeds/{id}",
     * description="Delete entered animal breed.",
     *     @OA\Parameter(
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(type="string"),
     *         @OA\Examples(example="int", value="1", summary="An int value."),
     *      ),
     * operationId="delete_animal_breed",
     * tags={"Admin - Animal Breeds"},
     * security={{"bearer_token":{}}},
     * @OA\Response(
     *    response=204,
     *    description="successful operation"
     * ),
     * )
     *)
    */
    public function destroy(AnimalBreed $animal_breed)
    {
        $animal_breed->delete();

        return response()->json(null, 204);
    }
}
