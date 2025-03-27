<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\BranchResource;
use App\Models\Branch;
use App\Models\Entity;
use Illuminate\Http\Request;
use Mosab\Translation\Models\Translation;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class BranchController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->middleware('permission:entities.read|entities.write|entities.delete')->only('index', 'show');
        $this->middleware('permission:entities.write')->only('store', 'update');
        $this->middleware('permission:entities.delete')->only('destroy');
    }

    /**
     * @OA\Get(
     * path="/admin/branches",
     * description="Get all branches",
     * operationId="get_all_branches",
     * tags={"Admin - Branches"},
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
     *    name="entity_id",
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
    public function index(Request $request)
    {
        $request->validate([
            'with_paginate'      => ['integer', 'in:0,1'],
            'per_page'           => ['integer', 'min:1'],
            'entity_id'          => ['integer', 'exists:entities,id'],
            'q'                  => ['string']
        ]);

        $q = Branch::with('entity')->latest();

        if ($request->entity_id)
            $q->where('entity_id', $request->entity_id);

        if($request->q)
        {
            $branches_ids = Translation::where('translatable_type', Branch::class)
                                        ->where('attribute', 'name')
                                        ->where('value', 'LIKE', '%'.$request->q.'%')
                                        ->groupBy('translatable_id')
                                        ->pluck('translatable_id');

            $q->where(function($query) use ($request, $branches_ids) {
                if (is_numeric($request->q))
                    $query->where('id', $request->q);
        
                $query->orWhereIn('id', $branches_ids);
            });
        }

        if($request->with_paginate === '0')
            $branches = $q->get();
        else
            $branches = $q->paginate($request->per_page ?? 10);

        return BranchResource::collection($branches);
    }

    /**
     * @OA\Post(
     * path="/admin/branches",
     * description="Add new branch.",
     * tags={"Admin - Branches"},
     * security={{"bearer_token": {} }},
     *   @OA\RequestBody(
     *       required=true,
     *       @OA\MediaType(
     *           mediaType="multipart/form-data",
     *           @OA\Schema(
     *              required={"address"},
     *              @OA\Property(property="entity_id", type="integer"),
     *              @OA\Property(property="address", type="string"),
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
            'entity_id'         => ['required', 'integer', 'exists:entities,id'],
            'address'           => ['required', 'string'],
        ]);

        $entity = Entity::find($request->entity_id);

        if($entity->used_branches == $entity->allowed_branches)
            throw new BadRequestHttpException(__('error_messages.branches_limit_reached'));
     
        $branch = Branch::create([
            'entity_id'         => $request->entity_id,
            'address'           => $request->address,
        ]);

        $entity->used_branches = $entity->used_branches -1;
        $entity->save();

        return response()->json(new BranchResource($branch), 200);
    }

    /**
     * @OA\Get(
     * path="/admin/branches/{id}",
     * description="Get branch information.",
     *     @OA\Parameter(
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(type="string"),
     *      ),
     * operationId="show_branch",
     * tags={"Admin - Branches"},
     * security={{"bearer_token": {} }},
     * @OA\Response(
     *    response=200,
     *    description="successful operation"
     * ),
     * )
     *)
    */
    public function show(Branch $branch)
    {
        return response()->json(new BranchResource($branch), 200);
    }

    /**
     * @OA\Post(
     * path="/admin/branches/{id}",
     * description="Edit branch.",
     *   @OA\Parameter(
     *     in="path",
     *     name="id",
     *     required=true,
     *     @OA\Schema(type="string"),
     *   ),
     *  tags={"Admin - Branches"},
     *  security={{"bearer_token": {} }},
     *   @OA\RequestBody(
     *       required=true,
     *       @OA\MediaType(
     *           mediaType="multipart/form-data",
     *           @OA\Schema(
     *              @OA\Property(property="address", type="string"),
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
    public function update(Request $request, Branch $branch)
    {
        $request->validate([
            'address'           => ['required', 'string'],
        ]);

        $branch->update([
            'address'           => $request->address,
        ]);

        return response()->json(new BranchResource($branch), 200);
    }

    /**
     * @OA\Delete(
     * path="/admin/branches/{id}",
     * description="Delete entered Branch.",
     *     @OA\Parameter(
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(type="string"),
     *         @OA\Examples(example="int", value="1", summary="An int value."),
     *      ),
     * operationId="delete_branch",
     * tags={"Admin - Branches"},
     * security={{"bearer_token":{}}},
     * @OA\Response(
     *    response=200,
     *    description="successful operation"
     * ),
     * )
     *)
    */
    public function destroy(Branch $branch)
    {
        $branch->delete();
        return response()->json(null, 204);
    }
}

