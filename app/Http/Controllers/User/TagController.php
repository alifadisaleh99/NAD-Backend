<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\TagRequest;
use App\Http\Resources\TagResource;
use App\Services\TagService;
use Illuminate\Http\Request;

class TagController extends Controller
{
    protected $tagService;

    public function __construct(TagService $tagService)
    {
        $this->middleware('auth:sanctum');

        $this->tagService = $tagService;
    }

    /**
     * @OA\Post(
     * path="/user/tags",
     * description="Edit tags for animal.",
     *  tags={"User - Tags"},
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
     *                 @OA\Property(property="deleted_tag_ids[0]",type="integer"),
     *                 @OA\Property(property="animal_id",type="integer"),
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

