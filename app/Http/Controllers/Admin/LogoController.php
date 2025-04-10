<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\LogoRequest;
use App\Http\Resources\LogoResource;
use App\Services\LogoService;

class LogoController extends Controller
{
    public $logoService;

    public function __construct(LogoService $logoService)
    {
        $this->middleware('auth:sanctum');
        $this->middleware('permission:logo.read|logo.write')->only('show');
        $this->middleware('permission:logo.write')->only('update');

        $this->logoService = $logoService;
    }
    
    /**
     * @OA\Get(
     * path="/admin/logo",
     * description="Get logo.",
     * operationId="show_logo",
     * tags={"Admin - Logo"},
     * security={{"bearer_token": {} }},
     * @OA\Response(
     *    response=200,
     *    description="successful operation"
     * ),
     * )
     *)
    */
    public function show()
    {
        $logo = $this->logoService->show(); 

        return response()->json(new LogoResource($logo), 200);
    }

    /**
     * @OA\Post(
     * path="/admin/logo",
     * description="Edit logo.",
     *  tags={"Admin - Logo"},
     *  security={{"bearer_token": {} }},
     *   @OA\RequestBody(
     *       required=true,
     *       @OA\MediaType(
     *           mediaType="multipart/form-data",
     *           @OA\Schema(
     *              required={"mobile_light_logo", "mobile_dark_logo", "light_logo", "dark_logo"},
     *              @OA\Property(property="mobile_light_logo", type="file"),
     *              @OA\Property(property="mobile_dark_logo", type="file"),
     *              @OA\Property(property="light_logo", type="file"),
     *              @OA\Property(property="dark_logo", type="file"),
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
    public function update(LogoRequest $request)
    {
        $logo = $this->logoService->update($request);

        return response()->json(new LogoResource($logo), 200);
    }
}
