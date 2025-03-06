<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\PetMarkResource;
use App\Models\PetMark;
use Illuminate\Http\Request;
use Mosab\Translation\Models\Translation;

class PetMarkController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->middleware('permission:petMarks.read|petMarks.write|petMarks.delete')->only('index', 'show');
        $this->middleware('permission:petMarks.write')->only('store', 'update');
        $this->middleware('permission:petMarks.delete')->only('destroy');
    }

    /**
     * @OA\Get(
     * path="/admin/pet-marks",
     * description="Get all pet marks",
     * operationId="get_all_pet_marks",
     * tags={"Admin - Pet Marks"},
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

        $q = PetMark::query()->latest();

        if($request->q)
        {
            $pet_marks_ids = Translation::where('translatable_type', PetMark::class)
                                        ->where('attribute', 'name')
                                        ->where('value', 'LIKE', '%'.$request->q.'%')
                                        ->groupBy('translatable_id')
                                        ->pluck('translatable_id');

            $q->where(function($query) use ($request, $pet_marks_ids) {
                if (is_numeric($request->q))
                    $query->where('id', $request->q);
        
                $query->orWhereIn('id', $pet_marks_ids);
            });
        }    

        if($request->with_paginate === '0')
          $pet_marks = $q->get();
        else
          $pet_marks = $q->paginate($request->per_page ?? 10);

        return PetMarkResource::collection($pet_marks);
    }

    /**
     * @OA\Post(
     * path="/admin/pet-marks",
     * description="Add new pet mark.",
     * tags={"Admin - Pet Marks"},
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
     
        $pet_mark = PetMark::create([
            'name'          => $request->name,
        ]);

        return response()->json(new PetMarkResource($pet_mark), 200);
    }

    /**
     * @OA\Get(
     * path="/admin/pet-marks/{id}",
     * description="Get pet mark information.",
     *     @OA\Parameter(
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(type="string"),
     *      ),
     * operationId="show_pet_mark",
     * tags={"Admin - Pet Marks"},
     * security={{"bearer_token": {} }},
     * @OA\Response(
     *    response=200,
     *    description="successful operation"
     * ),
     * )
     *)
    */
    public function show(PetMark $pet_mark)
    {
        return response()->json(new PetMarkResource($pet_mark), 200);
    }

    /**
     * @OA\Post(
     * path="/admin/pet-marks/{id}",
     * description="Edit pet mark.",
     *   @OA\Parameter(
     *     in="path",
     *     name="id",
     *     required=true,
     *     @OA\Schema(type="string"),
     *   ),
     *  tags={"Admin - Pet Marks"},
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
    public function update(Request $request,PetMark $pet_mark)
    {
        $request->validate([
            'name'           => ['required', 'array', translation_rule()],
        ]);

        $pet_mark->update([
            'name'          => $request->name,
        ]);

        return response()->json(new PetMarkResource($pet_mark), 200);
    }

    /**
     * @OA\Delete(
     * path="/admin/pet-marks/{id}",
     * description="Delete entered pet mark.",
     *     @OA\Parameter(
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(type="string"),
     *         @OA\Examples(example="int", value="1", summary="An int value."),
     *      ),
     * operationId="delete_pet_mark",
     * tags={"Admin - Pet Marks"},
     * security={{"bearer_token":{}}},
     * @OA\Response(
     *    response=200,
     *    description="successful operation"
     * ),
     * )
     *)
    */
    public function destroy(PetMark $pet_mark)
    {
        $pet_mark->delete();
        return response()->json(null, 204); 
    }
}
