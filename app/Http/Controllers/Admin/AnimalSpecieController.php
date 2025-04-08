<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AnimalSpecieRequest;
use App\Http\Requests\GetRequest;
use App\Models\AnimalSpecie;
use App\Http\Resources\AnimalSpecieResource;
use App\Services\AnimalSpecieService;

class AnimalSpecieController extends Controller
{
    public $animalSpecieService;

    public function __construct(AnimalSpecieService $animalSpecieService)
    {
        $this->middleware('auth:sanctum');
        $this->middleware('permission:animalSpecies.read|animalSpecies.write|animalSpecies.delete')->only('index', 'show');
        $this->middleware('permission:animalSpecies.write')->only('store', 'update');
        $this->middleware('permission:animalSpecies.delete')->only('destroy');

        $this->animalSpecieService = $animalSpecieService;
    }

    /**
     * @OA\Get(
     * path="/admin/animal-species",
     * description="Get all animal species",
     * operationId="get_all_animal_species",
     * tags={"Admin - Animal Species"},
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
     * @OA\Post(
     * path="/admin/animal-species",
     * description="Add new animal specie.",
     * tags={"Admin - Animal Species"},
     * security={{"bearer_token": {} }},
     *   @OA\RequestBody(
     *       required=true,
     *       @OA\MediaType(
     *           mediaType="multipart/form-data",
     *           @OA\Schema(
     *              required={"name[ar]", "category_id", "animal_type_id"},
     *              @OA\Property(property="category_id", type="integer"),
     *              @OA\Property(property="animal_type_id", type="integer"),
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
    public function store(AnimalSpecieRequest $request)
    {
        $animal_specie =  $this->animalSpecieService->create($request);

        return response()->json(new AnimalSpecieResource($animal_specie), 200);
    }

    /**
     * @OA\Get(
     * path="/admin/animal-species/{id}",
     * description="Get animal specie information.",
     *     @OA\Parameter(
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(type="string"),
     *      ),
     * operationId="show_animal_specie",
     * tags={"Admin - Animal Species"},
     * security={{"bearer_token": {} }},
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

    /**
     * @OA\Post(
     * path="/admin/animal-species/{id}",
     * description="Edit animal specie.",
     *   @OA\Parameter(
     *     in="path",
     *     name="id",
     *     required=true,
     *     @OA\Schema(type="string"),
     *   ),
     *  tags={"Admin - Animal Species"},
     *  security={{"bearer_token": {} }},
     *   @OA\RequestBody(
     *       required=true,
     *       @OA\MediaType(
     *           mediaType="multipart/form-data",
     *           @OA\Schema(
     *              @OA\Property(property="category_id", type="integer"),
     *              @OA\Property(property="animal_type_id", type="integer"),
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
    public function update(AnimalSpecieRequest $request, AnimalSpecie $animal_specie)
    {
        $this->animalSpecieService->update($request, $animal_specie);

        return response()->json(new AnimalSpecieResource($animal_specie), 200);
    }

    /**
     * @OA\Delete(
     * path="/admin/animal-species/{id}",
     * description="Delete entered animal specie.",
     *     @OA\Parameter(
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(type="string"),
     *         @OA\Examples(example="int", value="1", summary="An int value."),
     *      ),
     * operationId="delete_animal_specie",
     * tags={"Admin - Animal Species"},
     * security={{"bearer_token":{}}},
     * @OA\Response(
     *    response=204,
     *    description="successful operation"
     * ),
     * )
     *)
    */
    public function destroy(AnimalSpecie $animal_specie)
    {
        $animal_specie->delete();

        return response()->json(null, 204);
    }
}
