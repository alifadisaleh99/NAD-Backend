<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\TagRequest;
use App\Http\Resources\TagResource;
use App\Services\TagService;

class TagController extends Controller
{
    protected $tagService;

    public function __construct(TagService $tagService)
    {
        $this->middleware('auth:sanctum');
        $this->middleware('permission:tags.read|tags.write|tags.delete')->only('index', 'show');
        $this->middleware('permission:tags.write')->only('store', 'update');
        $this->middleware('permission:tags.delete')->only('destroy');

        $this->tagService = $tagService;
    }

    /**
     * @OA\Post(
     * path="/admin/tags",
     * description="Edit tags for animal.",
     *  tags={"Admin - Tags"},
     *  security={{"bearer_token": {} }},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                 @OA\Property(property="tags[0][id]", type="integer"),
     *                 @OA\Property(property="tags[0][tag_type_id]", type="integer"),
     *                 @OA\Property(property="tags[0][factory_number]", type="string"),
     *                 @OA\Property(property="tags[0][number]", type="string"),
     *                 @OA\Property(property="tags[0][status]", type="integer", enum={"0"," 1"}),
     *                 @OA\Property(property="deleted_tag_ids",type="array",@OA\Items(type="integer")),
     *                 @OA\Property(property="_method", type="string", example="PUT")
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *         response="200",
     *         description="successful operation",
     *     ),
     * )
     * )
     */
    public function update(TagRequest $request)
    {
        $tags = $this->tagService->update($request);


        return response()->json(TagResource::collection($tags), 200);
    }
}
