<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\GetRequest;
use App\Http\Resources\TagTypeResource;
use App\Models\TagType;
use App\Services\TagTypeService;
use Illuminate\Http\Request;

class TagTypeController extends Controller
{
    protected $tagTypeService;

    public function __construct(TagTypeService $tagTypeService)
    {
        $this->middleware('auth:sanctum');

        $this->tagTypeService = $tagTypeService;
    }

    /**
     * @OA\Get(
     * path="/user/tag-types",
     * description="Get all tag types",
     * operationId="get_all_tag_types_for_user",
     * tags={"User - Tag Types"},
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
        $tag_types = $this->tagTypeService->getAllTagTypes($request);

        return TagTypeResource::collection($tag_types);
    }

    /**
     * @OA\Get(
     * path="/user/tag-types/{id}",
     * description="Get tag type information.",
     *     @OA\Parameter(
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(type="string"),
     *      ),
     * operationId="show_tag_type_for_user",
     * tags={"User - Tag Types"},
     * security={{"bearer_token": {} }},
     * @OA\Response(
     *    response=200,
     *    description="successful operation"
     * ),
     * )
     *)
     */

    public function show(TagType $tag_type)
    {
        return response()->json(new TagTypeResource($tag_type), 200);
    }
}
