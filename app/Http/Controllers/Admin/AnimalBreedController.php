<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\AnimalBreedResource;
use App\Models\AnimalBreed;
use Illuminate\Http\Request;
use Mosab\Translation\Models\Translation;

class AnimalBreedController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->middleware('permission:animalBreeds.read|animalBreeds.write|animalBreeds.delete')->only('index', 'show');
        $this->middleware('permission:animalBreeds.write')->only('store', 'update');
        $this->middleware('permission:animalBreeds.delete')->only('destroy');
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
            'q'                  => ['string']
        ]);

        $q = AnimalBreed::query()->latest();

        if($request->q)
        {
            $animal_breeds_ids = Translation::where('translatable_type', AnimalBreed::class)
                                        ->where('attribute', 'name')
                                        ->where('value', 'LIKE', '%'.$request->q.'%')
                                        ->groupBy('translatable_id')
                                        ->pluck('translatable_id');

            $q->where(function($query) use ($request, $animal_breeds_ids) {
                if (is_numeric($request->q))
                    $query->where('id', $request->q);
        
                $query->orWhereIn('id', $animal_breeds_ids);
            });
        }

        if($request->with_paginate === '0')
            $animal_breeds = $q->get();
        else
            $animal_breeds = $q->paginate($request->per_page ?? 10);

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
     *              required={"name[ar]"},
     *              @OA\Property(property="category_id", type="integer"),
     *              @OA\Property(property="animal_type_id", type="integer"),
     *              @OA\Property(property="animal_specie_id", type="integer"),
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
            'animal_specie_id'      => ['required', 'integer', 'exists:animal_species,id'],
            'name'                => ['required', 'array', translation_rule()],
        ]);
     
        $animal_specie = AnimalBreed::create([
            'category_id'       => $request->category_id,
            'animal_type_id'    => $request->animal_type_id,
            'animal_specie_id'  => $request->animal_specie_id,
            'name'              => $request->name,
        ]);

        return response()->json(new AnimalBreedResource($animal_specie), 200);
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
        $animal_breed->load(['category', 'animal_type', 'animal_specie']);
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
     *              @OA\Property(property="animal_type_id", type="integer"),
     *              @OA\Property(property="animal_specie_id", type="integer"),
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
    public function update(Request $request, AnimalBreed $animal_breed)
    {
        $request->validate([
            'category_id'       => ['required', 'integer', 'exists:categories,id'],
            'animal_type_id'    => ['required', 'integer', 'exists:animal_types,id'],
            'animal_specie_id'  => ['required', 'integer', 'exists:animal_species,id'],
            'name'              => ['required', 'array', translation_rule()],
        ]);

        $animal_breed->update([
            'category_id'       => $request->category_id,
            'animal_type_id'    => $request->animal_type_id,
            'animal_specie_id'  => $request->animal_specie_id,
            'name'              => $request->name,
        ]);

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
     *    response=200,
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
