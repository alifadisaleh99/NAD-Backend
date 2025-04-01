<?php

namespace App\Http\Controllers;

use App\Http\Requests\GetRequest;
use App\Http\Resources\AnimalSpecieResource;
use App\Models\AnimalSpecie;
use App\Services\AnimalSpecieService;
use Illuminate\Http\Request;

class AnimalSpecieController extends Controller
{
    public  $animalSpecieService;

    public function __construct(AnimalSpecieService $animalSpecieService)
    {
        $this->animalSpecieService = $animalSpecieService;
    }
    /**
     * @OA\Get(
     * path="/animal-species",
     * description="Get all animal species",
     * operationId="get_all_animal_species_for_user",
     * tags={"User - Animal Species"},
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
     *  @OA\Parameter(
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
        $animal_species = $this->animalSpecieService->getAllAnimalSpecies($request);

        return AnimalSpecieResource::collection($animal_species);
    }
    /**
     * @OA\Get(
     * path="/animal-species/{id}",
     * description="Get animal specie information.",
     *     @OA\Parameter(
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(type="string"),
     *      ),
     * operationId="show_animal_specie_for_user",
     * tags={"User - Animal Species"},
     * @OA\Response(
     *    response=200,
     *    description="successful operation"
     * ),
     * )
     *)
     */
    public function show(AnimalSpecie $animal_specie)
    {
        $animal_specie->load(['category', 'animal_type']);
        return response()->json(new AnimalSpecieResource($animal_specie), 200);
    }
}
