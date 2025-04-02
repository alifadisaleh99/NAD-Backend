<?php

namespace App\Http\Controllers;

use App\Http\Requests\GetRequest;
use App\Http\Resources\PetMarkResource;
use App\Models\PetMark;
use App\Services\PetMarkService;
use Illuminate\Http\Request;

class PetMarkController extends Controller
{
    public $petMarkService;

    public function __construct(PetMarkService $petMarkService)
    {
        $this->petMarkService = $petMarkService;
    }

    /**
     * @OA\Get(
     * path="/pet-marks",
     * description="Get all pet marks",
     * operationId="get_all_pet_marks_for_user",
     * tags={"User - Pet Marks"},
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
    public function index(GetRequest $request)
    {
        $pet_marks = $this->petMarkService->getAllPetMarks($request);

        return PetMarkResource::collection($pet_marks);
    }

    /**
     * @OA\Get(
     * path="/pet-marks/{id}",
     * description="Get pet mark information.",
     *     @OA\Parameter(
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(type="string"),
     *      ),
     * operationId="show_pet_mark_for_user",
     * tags={"User - Pet Marks"},
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
}
