<?php

namespace App\Http\Controllers;

use App\Http\Requests\GetRequest;
use App\Http\Resources\AnimalBreedResource;
use App\Models\AnimalBreed;
use App\Services\AnimalBreedService;

class AnimalBreedController extends Controller
{
    public  $animalBreedService;

    public function __construct(AnimalBreedService $animalBreedService)
    {
        $this->animalBreedService = $animalBreedService;
    }
    
     /**
     * @OA\Get(
     * path="/animal-breeds",
     * description="Get all animal breeds",
     * operationId="get_all_animal_breeds_for_user",
     * tags={"User - Animal Breeds"},
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
     *    name="animal_type_id",
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
     * @OA\Get(
     * path="/animal-breeds/{id}",
     * description="Get animal breed information.",
     *     @OA\Parameter(
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(type="string"),
     *      ),
     * operationId="show_animal_breed_for_user",
     * tags={"User - Animal Breeds"},
     * @OA\Response(
     *    response=200,
     *    description="successful operation"
     * ),
     * )
     *)
    */
    public function show(AnimalBreed $animal_breed)
    {
        $animal_breed->load(['category', 'animal_type', 'animal_specie']);

        return response()->json(new AnimalBreedResource($animal_breed), 200);
    }
}
