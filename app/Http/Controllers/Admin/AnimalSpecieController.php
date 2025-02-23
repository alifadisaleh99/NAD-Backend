<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AnimalSpecie;
use App\Http\Resources\AnimalSpecieResource;
use Mosab\Translation\Models\Translation;

class AnimalSpecieController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->middleware('permission:animalSpecies.read|animalSpecies.write|animalSpecies.delete')->only('index', 'show');
        $this->middleware('permission:animalSpecies.write')->only('store', 'update');
        $this->middleware('permission:animalSpecies.delete')->only('destroy');
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
    public function index(Request $request)
    {
        $request->validate([
            'with_paginate'      => ['integer', 'in:0,1'],
            'per_page'           => ['integer', 'min:1'],
            'category_id'        => ['integer', 'exists:categories,id'],
            'animal_type_id'     => ['integer', 'exists:animal_types,id'],
            'q'                  => ['string']
        ]);

        $q = AnimalSpecie::query()->with(['category', 'animal_type'])->latest();

        if($request->category_id)
            $q->where('category_id', $request->category_id);
        if($request->animal_type_id)
            $q->where('animal_type_id', $request->animal_type_id);

        if($request->q)
        {
            $animal_species_ids = Translation::where('translatable_type', AnimalSpecie::class)
                                        ->where('attribute', 'name')
                                        ->where('value', 'LIKE', '%'.$request->q.'%')
                                        ->groupBy('translatable_id')
                                        ->pluck('translatable_id');

            $q->where(function($query) use ($request, $animal_species_ids) {
                if (is_numeric($request->q))
                    $query->where('id', $request->q);
        
                $query->orWhereIn('id', $animal_species_ids);
            });
        }

        if($request->with_paginate === '0')
            $animal_species = $q->get();
        else
            $animal_species = $q->paginate($request->per_page ?? 10);

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
     *              required={"name[ar]"},
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
    public function store(Request $request)
    {
        $request->validate([
            'category_id'         => ['required', 'integer', 'exists:categories,id'],
            'animal_type_id'      => ['required', 'integer', 'exists:animal_types,id'],
            'name'                => ['required', 'array', translation_rule()],
        ]);
     
        $animal_specie = AnimalSpecie::create([
            'category_id'       => $request->category_id,
            'animal_type_id'    => $request->animal_type_id,
            'name'              => $request->name,
        ]);

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
    public function update(Request $request, AnimalSpecie $animal_specie)
    {
        $request->validate([
            'category_id'       => ['required', 'integer', 'exists:categories,id'],
            'animal_type_id'    => ['required', 'integer', 'exists:animal_types,id'],
            'name'              => ['required', 'array', translation_rule()],
        ]);

        $animal_specie->update([
            'category_id'       => $request->category_id,
            'animal_type_id'    => $request->animal_type_id,
            'name'              => $request->name,
        ]);

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
     *    response=200,
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
