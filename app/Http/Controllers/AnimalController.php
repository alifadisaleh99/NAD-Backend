<?php

namespace App\Http\Controllers;

use App\Http\Requests\GetRequest;
use App\Http\Resources\AnimalResource;
use App\Services\AnimalService;
use Illuminate\Http\Request;

class AnimalController extends Controller
{
    public  $animalService;

    public function __construct(AnimalService $animalService)
    {
        $this->animalService = $animalService;
    }
     /**
     * @OA\Get(
     * path="/animals",
     * description="Get animal by uaid or tag number",
     * operationId="get_animal_to_user",
     * tags={"User - Animals"},
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
}
