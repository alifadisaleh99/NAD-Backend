<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\TransferRequest;
use App\Models\Animal;
use App\Services\AnimalService;


class AnimalController extends Controller
{
    public  $animalService;

    public function __construct(AnimalService $animalService)
    {
        $this->middleware('auth:sanctum');
      //   $this->middleware('permission:animals.transfer')->only('generateTransferToken', 'acceptTransfer');
        $this->middleware('owner.animal')->only(['generateTransferToken']);

        $this->animalService = $animalService;
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
        $this->animalService->acceptTransfer($request);

        return response()->json(200);
    }
}
