<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\GetRequest;
use App\Http\Resources\AnimalStatusResource;
use App\Models\AnimalStatus;
use Illuminate\Http\Request;

class AnimalStatusController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->middleware('permission:animalStatuses.read|animalStatuses.write|animalStatuses.delete')->only('index', 'show');
        $this->middleware('permission:animalStatuses.write')->only('store', 'update');
        $this->middleware('permission:animalStatuses.delete')->only('destroy');
    }

    /**
     * @OA\Get(
     * path="/admin/animal-statuses",
     * description="Get all animal statuses",
     * operationId="get_all_animal_statuses",
     * tags={"Admin - Animal Statuses"},
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
     *    name="animal_id",
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
        $q = AnimalStatus::query()->with(['animal', 'user'])->latest();

        if($request->animal_id)
            $q->where('animal_id', $request->animal_id);

     /*   if($request->q)
        {

            $q->where(function($query) use ($request, $animal_species_ids) {
                if (is_numeric($request->q))
                    $query->where('id', $request->q);
        
                $query->orWhereIn('id', $animal_species_ids);
            });
        }*/

        if($request->with_paginate === '0')
            $animal_statuses = $q->get();
        else
            $animal_statuses = $q->paginate($request->per_page ?? 10);

        return  AnimalStatusResource::collection($animal_statuses);
    }

    /**
     * @OA\Post(
     * path="/admin/animal-statuses",
     * description="Add new animal status.",
     * tags={"Admin - Animal Statuses"},
     * security={{"bearer_token": {} }},
     *   @OA\RequestBody(
     *       required=true,
     *       @OA\MediaType(
     *           mediaType="multipart/form-data",
     *           @OA\Schema(
     *              required={"description[ar]"},
     *              @OA\Property(property="animal_id", type="integer"),
     *              @OA\Property(property="status", type="string", enum={"lost", "found", "adopt", "dead", "transfer"}),
     *              @OA\Property(property="description[en]", type="string"),
     *              @OA\Property(property="description[ar]", type="string"),
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
            'status'              => ['required', 'string', 'in:lost,found,adopt,dead,transfer'],
            'animal_id'           => ['required', 'integer', 'exists:animals,id'],
            'description'         => ['required', 'array', translation_rule()],
        ]);
     
        $animal_status = AnimalStatus::create([
            'status'            => $request->status,
            'animal_id'         => $request->animal_id,
            'user_id'           => auth()->id(),
            'description'       => $request->description,
        ]);

        return response()->json(new AnimalStatusResource($animal_status), 200);
    }

    /**
     * @OA\Get(
     * path="/admin/animal-statuses/{id}",
     * description="Get animal status information.",
     *     @OA\Parameter(
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(type="string"),
     *      ),
     * operationId="show_animal_status",
     * tags={"Admin - Animal Statuses"},
     * security={{"bearer_token": {} }},
     * @OA\Response(
     *    response=200,
     *    description="successful operation"
     * ),
     * )
     *)
    */
    public function show(AnimalStatus $animal_status)
    {
        $animal_status->load(['user', 'animal']);
        return response()->json(new AnimalStatusResource($animal_status), 200);
    }

    /**
     * @OA\Post(
     * path="/admin/animal-statuses/{id}",
     * description="Edit animal status.",
     *   @OA\Parameter(
     *     in="path",
     *     name="id",
     *     required=true,
     *     @OA\Schema(type="string"),
     *   ),
     *  tags={"Admin - Animal Statuses"},
     * operationId="edit_animal_status",
     *  security={{"bearer_token": {} }},
     *   @OA\RequestBody(
     *       required=true,
     *       @OA\MediaType(
     *           mediaType="multipart/form-data",
     *           @OA\Schema(
     *              @OA\Property(property="animal_id", type="integer"),
     *              @OA\Property(property="status", type="string", enum={"lost", "found", "adopt", "dead", "transfer"}),
     *              @OA\Property(property="description[en]", type="string"),
     *              @OA\Property(property="description[ar]", type="string"),
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
    public function update(Request $request, AnimalStatus $animal_status)
    {
        $request->validate([
            'status'              => ['required', 'string', 'in:lost,found,adopt,dead,transfer'],
            'animal_id'           => ['required', 'integer', 'exists:animals,id'],
            'description'         => ['required', 'array', translation_rule()],
        ]);

        $animal_status->update([
            'status'            => $request->status,
            'animal_id'         => $request->animal_id,
            'user_id'           => auth()->id(),
            'description'       => $request->description,
        ]);

        return response()->json(new AnimalStatusResource($animal_status), 200);
    }

    /**
     * @OA\Delete(
     * path="/admin/animal-statuses/{id}",
     * description="Delete entered animal status.",
     *     @OA\Parameter(
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(type="string"),
     *         @OA\Examples(example="int", value="1", summary="An int value."),
     *      ),
     * operationId="delete_animal_status",
     * tags={"Admin - Animal Statuses"},
     * security={{"bearer_token":{}}},
     * @OA\Response(
     *    response=200,
     *    description="successful operation"
     * ),
     * )
     *)
    */
    public function destroy(AnimalStatus $animal_status)
    {
        $animal_status->delete();
        return response()->json(null, 204);
    }
}
