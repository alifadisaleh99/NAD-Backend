<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\GetRequest;
use App\Http\Resources\VaccinationResource;
use App\Models\Vaccination;
use App\Services\VaccinationService;

class VaccinationController extends Controller
{
    protected $vaccinationService;

    public function __construct(VaccinationService $vaccinationService)
    {
        $this->middleware('auth:sanctum');

        $this->vaccinationService = $vaccinationService;
    }

    /**
     * @OA\Get(
     * path="/user/vaccinations",
     * description="Get all vaccinations",
     * operationId="get_all_animal_vaccinations",
     * tags={"User - Vaccinations"},
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
        $vaccinations  = $this->vaccinationService->getAllVaccinations($request);

        return VaccinationResource::collection($vaccinations);
    }

   /**
     * @OA\Get(
     * path="/user/vaccinations/{id}",
     * description="Get vaccination information.",
     *     @OA\Parameter(
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(type="string"),
     *      ),
     * operationId="show_vaccination",
     * tags={"User - Vaccinations"},
     * security={{"bearer_token": {} }},
     * @OA\Response(
     *    response=200,
     *    description="successful operation"
     * ),
     * )
     *)
     */

     public function show(Vaccination $vaccination)
     {
        $vaccination->load(['animal']);

         return response()->json(new VaccinationResource($vaccination), 200);
     } 
}
