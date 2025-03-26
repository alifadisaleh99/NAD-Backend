<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\GetRequest;
use App\Http\Requests\TransferRequest;
use App\Http\Resources\AnimalResource;
use App\Http\Resources\OwnershipRecordResource;
use App\Models\Animal;
use App\Services\AnimalService;


class AnimalController extends Controller
{
    public  $animalService;

    public function __construct(AnimalService $animalService)
    {
        $this->middleware('auth:sanctum');
        //   $this->middleware('permission:animals.transfer')->only('generateTransferToken', 'acceptTransfer');
        //  $this->middleware('permission:ownershipRecords.read')->only('ownershipRecords');
       // $this->middleware('owner.animal')->only(['generateTransferToken', 'ownershipRecords']);

        $this->animalService = $animalService;
    }

    /**
     * @OA\Get(
     * path="/user/animals",
     * description="Get animal by uaid or tag number",
     * operationId="get_animal_to_user",
     * tags={"User - Animals"},
     *   security={{"bearer_token": {} }},
     * @OA\Parameter(
     *    in="query",
     *    name="uaid",
     *    required=false,
     *    @OA\Schema(type="string"),
     * ),
     * @OA\Parameter(
     *    in="query",
     *    name="tag_number",
     *    required=false,
     *    @OA\Schema(type="string"),
     * ),
     *   @OA\Response(
     *     response=200,
     *     description="Success",
     *  )
     *  )
     */
    public function getAnimal(GetRequest $request)
    { 
        $animal = $this->animalService->getAnimalByUaidAndTagNumber($request);

        if(!$animal)
           return response()->json(['message' => __('error_messages.animal_not_found')], 404); 

        return response()->json(new AnimalResource($animal), 200);   
    }
          
    /**
     * @OA\Post(
     * path="/user/animals/{id}/generate-token",
     * description="Generate token.",
     * @OA\Parameter(
     *     in="path",
     *     name="id",
     *     required=true,
     *     @OA\Schema(type="string"),
     * ),
     * tags={"User - Animals"},
     * security={{"bearer_token": {}}},
     * @OA\Response(
     *         response="200",
     *         description="successful operation",
     *     ),
     * )
     */

    public function generateTransferToken(Animal $animal)
    {
        $token = $this->animalService->generateTransferToken($animal);

        return response()->json(['token' => $token], 200);
    }

    /**
     * @OA\Post(
     * path="/user/animals/accept-transfer",
     * description="Accept animal transfer.",
     * tags={"User - Animals"},
     * security={{"bearer_token": {}}},
     *   @OA\RequestBody(
     *       required=true,
     *       @OA\MediaType(
     *           mediaType="multipart/form-data",
     *           @OA\Schema(
     *              required={"token"},
     *              @OA\Property(property="token", type="string"),
     *           )
     *       )
     *   ),
     * @OA\Response(
     *         response="200",
     *         description="successful operation",
     *     ),
     * )
     */
    public function acceptTransfer(TransferRequest $request)
    {
        $transfer_information = $this->animalService->acceptTransfer($request);

        return response()->json(['transfer_information' => $transfer_information], 200);
    }

    /**
     * @OA\Get(
     * path="/user/animals/{id}/ownership-records",
     * description="Get all ownership records for animal",
     * operationId="get_all_ownership_records_for_user",
     * tags={"User - Animals"},
     *   security={{"bearer_token": {} }},
     * @OA\Parameter(
     *     in="path",
     *     name="id",
     *     required=true,
     *     @OA\Schema(type="string"),
     * ),
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
     *    name="owner_id",
     *    required=false,
     *    @OA\Schema(type="integer"),
     * ),
     * @OA\Response(
     *     response=200,
     *     description="Success",
     *  )
     *  )
     * */

    public function ownershipRecords(GetRequest $request, Animal $animal)
    { 
        $ownership_records = $this->animalService->getOwnershipRecords($request, $animal);

        return OwnershipRecordResource::collection($ownership_records);
    }
}
