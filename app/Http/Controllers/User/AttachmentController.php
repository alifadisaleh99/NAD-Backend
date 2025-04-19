<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\GetRequest;
use App\Http\Resources\AttachmentResource;
use App\Models\Attachment;
use App\Services\AttachmentService;

class AttachmentController extends Controller
{
    protected $attachmentService;

    public function __construct(AttachmentService $attachmentService)
    {
        $this->middleware('auth:sanctum');

        $this->attachmentService = $attachmentService;
    }

    /**
     * @OA\Get(
     * path="/user/attachments",
     * description="Get all attachments",
     * operationId="get_all_animal_attachments",
     * tags={"User - Attachments"},
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
        $attachments = $this->attachmentService->getAllAttachments($request);

        return AttachmentResource::collection($attachments);
    }

   /**
     * @OA\Get(
     * path="/user/attachments/{id}",
     * description="Get attachment information.",
     *     @OA\Parameter(
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(type="string"),
     *      ),
     * operationId="show_attachment",
     * tags={"User - Attachments"},
     * security={{"bearer_token": {} }},
     * @OA\Response(
     *    response=200,
     *    description="successful operation"
     * ),
     * )
     *)
     */

     public function show(Attachment $attachment)
     {
        $attachment->load(['animal']);

         return response()->json(new AttachmentResource($attachment), 200);
     } 
}
