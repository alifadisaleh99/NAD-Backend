<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\GetRequest;
use App\Http\Resources\TagResource;
use App\Models\Tag;
use App\Services\TagService;

class TagController extends Controller
{
    protected $tagService;

    public function __construct(TagService $tagService)
    {
        $this->middleware('auth:sanctum');

        $this->tagService = $tagService;
    }

    /**
     * @OA\Get(
     * path="/user/tags",
     * description="Get all tags",
     * operationId="get_all_animal_tags",
     * tags={"User - Tags"},
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
     *    name="tag_number",
     *    required=false,
     *    @OA\Schema(type="string"),
     * ),
     * @OA\Parameter(
     *    in="query",
     *    name="tag_type_id",
     *    required=false,
     *    @OA\Schema(type="integerr"),
     * ),
     *   @OA\Response(
     *     response=200,
     *     description="Success",
     *  )
     *  )
     */
    public function index(GetRequest $request)
    {
        $tags = $this->tagService->getAllTags($request);

        return TagResource::collection($tags);
    }

   /**
     * @OA\Get(
     * path="/user/tags/{id}",
     * description="Get tag information.",
     *     @OA\Parameter(
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(type="string"),
     *      ),
     * operationId="show_tag",
     * tags={"User - Tags"},
     * security={{"bearer_token": {} }},
     * @OA\Response(
     *    response=200,
     *    description="successful operation"
     * ),
     * )
     *)
     */

     public function show(Tag $tag)
     {
        $tag->load(['animal', 'tag_type']);

         return response()->json(new TagResource($tag), 200);
     } 
}

