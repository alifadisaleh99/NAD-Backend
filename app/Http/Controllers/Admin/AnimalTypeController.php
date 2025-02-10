<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\AnimalTypeResource;
use App\Models\AnimalType;
use Illuminate\Http\Request;
use Mosab\Translation\Models\Translation;

class AnimalTypeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->middleware('permission:animalTypes.read|animalTypes.write|animalTypes.delete')->only('index', 'show');
        $this->middleware('permission:animalTypes.write')->only('store', 'update');
        $this->middleware('permission:animalTypes.delete')->only('destroy');
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

        $q = AnimalType::query()->latest();

        if($request->q)
        {
            $animal_types_ids = Translation::where('translatable_type', AnimalType::class)
                                        ->where('attribute', 'name')
                                        ->where('value', 'LIKE', '%'.$request->q.'%')
                                        ->groupBy('translatable_id')
                                        ->pluck('translatable_id');

            $q->where(function($query) use ($request, $animal_types_ids) {
                if (is_numeric($request->q))
                    $query->where('id', $request->q);
        
                $query->orWhereIn('id', $animal_types_ids);
            });
        }

        if($request->with_paginate === '0')
            $animal_types = $q->get();
        else
            $animal_types = $q->paginate($request->per_page ?? 10);

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
            'name'           => ['required', 'array', translation_rule()],
        ]);
     
        $animal_type = AnimalType::create([
            'name'          => $request->name,
        ]);

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
    public function update(Request $request, AnimalType $animal_type)
    {
        $request->validate([
            'name'           => ['required', 'array', translation_rule()],
        ]);

        $animal_type->update([
            'name'         => $request->name,
        ]);

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
     *    response=200,
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
