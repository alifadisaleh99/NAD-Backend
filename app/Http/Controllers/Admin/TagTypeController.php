<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\GetRequest;
use App\Http\Requests\TagTypeRequest;
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
        $this->middleware('permission:tagTypes.read|tagTypes.write|tagTypes.delete')->only('index', 'show');
        $this->middleware('permission:tagTypes.write')->only('store', 'update');
        $this->middleware('permission:tagTypes.delete')->only('destroy');

        $this->tagTypeService = $tagTypeService;
    }

    /**
     * @OA\Get(
     * path="/admin/tag-types",
     * description="Get all tag types",
     * operationId="get_all_tag_types",
     * tags={"Admin - Tag Types"},
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
     * @OA\Post(
     * path="/admin/tag-types",
     * description="Add new tag type.",
     * tags={"Admin - Tag Types"},
     * security={{"bearer_token": {} }},
     *   @OA\RequestBody(
     *       required=true,
     *       @OA\MediaType(
     *           mediaType="multipart/form-data",
     *           @OA\Schema(
     *              required={"name[ar]"},
     *              @OA\Property(property="name[en]", type="string"),
     *              @OA\Property(property="name[ar]", type="string"),
     *              @OA\Property(property="icon", type="file"),
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

    public function store(TagTypeRequest $request)
    {
        $tag_type = $this->tagTypeService->create($request);

        return response()->json(new TagTypeResource($tag_type), 200);
    }

    /**
     * @OA\Get(
     * path="/admin/tag-types/{id}",
     * description="Get tag type information.",
     *     @OA\Parameter(
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(type="string"),
     *      ),
     * operationId="show_tag_type",
     * tags={"Admin - Tag Types"},
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

    /**
     * @OA\Post(
     * path="/admin/tag-types/{id}",
     * description="Edit tag type.",
     *   @OA\Parameter(
     *     in="path",
     *     name="id",
     *     required=true,
     *     @OA\Schema(type="string"),
     *   ),
     *  tags={"Admin - Tag Types"},
     *  security={{"bearer_token": {} }},
     *   @OA\RequestBody(
     *       required=true,
     *       @OA\MediaType(
     *           mediaType="multipart/form-data",
     *           @OA\Schema(
     *              @OA\Property(property="name[en]", type="string"),
     *              @OA\Property(property="name[ar]", type="string"),
     *              @OA\Property(property="icon", type="file"),
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

    public function update(TagTypeRequest $request, TagType $tag_type)
    {
        $tag_type = $this->tagTypeService->update($request, $tag_type);
          

        return response()->json(new TagTypeResource($tag_type), 200);
    }

    /**
     * @OA\Delete(
     * path="/admin/tag-types/{id}",
     * description="Delete entered tag type.",
     *     @OA\Parameter(
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(type="string"),
     *         @OA\Examples(example="int", value="1", summary="An int value."),
     *      ),
     * operationId="delete_tag_type",
     * tags={"Admin - Tag Types"},
     * security={{"bearer_token":{}}},
     * @OA\Response(
     *    response=200,
     *    description="successful operation"
     * ),
     * )
     *)
    */

    public function destroy(TagType $tag_type)
    {
        $this->tagTypeService->delete($tag_type);
        
        return response()->json(null, 204); 
    }
}
