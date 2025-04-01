<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\GetRequest;
use App\Http\Resources\ColorResource;
use App\Models\Color;
use App\Services\ColorService;
use Illuminate\Http\Request;
use Mosab\Translation\Models\Translation;

class ColorController extends Controller
{
    public $colorService;

    public function __construct(ColorService $colorService)
    {
        $this->middleware('auth:sanctum');
        $this->middleware('permission:colors.read|colors.write|colors.delete')->only('index', 'show');
        $this->middleware('permission:colors.write')->only('store', 'update');
        $this->middleware('permission:colors.delete')->only('destroy');

        $this->colorService = $colorService;
    }

    /**
     * @OA\Get(
     * path="/admin/colors",
     * description="Get all colors",
     * operationId="get_all_colors",
     * tags={"Admin - Colors"},
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
    public function index(GetRequest $request)
    {
          $colors = $this->colorService->getAllColors($request);

        return ColorResource::collection($colors);
    }

    /**
     * @OA\Post(
     * path="/admin/colors",
     * description="Add new color.",
     * tags={"Admin - Colors"},
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
     
        $color = Color::create([
            'name'          => $request->name,
        ]);

        return response()->json(new ColorResource($color), 200);
    }

    /**
     * @OA\Get(
     * path="/admin/colors/{id}",
     * description="Get color information.",
     *     @OA\Parameter(
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(type="string"),
     *      ),
     * operationId="show_color",
     * tags={"Admin - Colors"},
     * security={{"bearer_token": {} }},
     * @OA\Response(
     *    response=200,
     *    description="successful operation"
     * ),
     * )
     *)
    */
    public function show(Color $color)
    {
        return response()->json(new ColorResource($color), 200);
    }

    /**
     * @OA\Post(
     * path="/admin/colors/{id}",
     * description="Edit color.",
     *   @OA\Parameter(
     *     in="path",
     *     name="id",
     *     required=true,
     *     @OA\Schema(type="string"),
     *   ),
     *  tags={"Admin - Colors"},
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
    public function update(Request $request, Color $color)
    {
        $request->validate([
            'name'           => ['required', 'array', translation_rule()],
        ]);

        $color->update([
            'name'          => $request->name,
        ]);

        return response()->json(new ColorResource($color), 200);
    }

    /**
     * @OA\Delete(
     * path="/admin/colors/{id}",
     * description="Delete entered color.",
     *     @OA\Parameter(
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(type="string"),
     *         @OA\Examples(example="int", value="1", summary="An int value."),
     *      ),
     * operationId="delete_color",
     * tags={"Admin - Colors"},
     * security={{"bearer_token":{}}},
     * @OA\Response(
     *    response=200,
     *    description="successful operation"
     * ),
     * )
     *)
    */
    public function destroy(Color $color)
    {
        $color->delete();
        return response()->json(null, 204); 
    }
}
