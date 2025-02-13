<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\EntityResource;
use App\Http\Resources\UserResource;
use App\Models\Entity;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Mosab\Translation\Models\Translation;

class EntityController extends Controller
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
     * path="/admin/entities",
     * description="Get all entities",
     * operationId="get_all_entities",
     * tags={"Admin - Entities"},
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
    public function index(Request $request)
    {
        $request->validate([
            'with_paginate'      => ['integer', 'in:0,1'],
            'per_page'           => ['integer', 'min:1'],
            'q'                  => ['string']
        ]);

        $q = Entity::query()->latest();

        if($request->q)
        {
            $entities_ids = Translation::where('translatable_type', Entity::class)
                                        ->where('attribute', 'name')
                                        ->where('value', 'LIKE', '%'.$request->q.'%')
                                        ->groupBy('translatable_id')
                                        ->pluck('translatable_id');

            $q->where(function($query) use ($request, $entities_ids) {
                if (is_numeric($request->q))
                    $query->where('id', $request->q);
        
                $query->orWhereIn('id', $entities_ids);
            });
        }

        if($request->with_paginate === '0')
            $entities = $q->get();
        else
            $entities = $q->paginate($request->per_page ?? 10);

        return EntityResource::collection($entities);
    }

    /**
     * @OA\Post(
     * path="/admin/entities",
     * description="Add new entity.",
     * tags={"Admin - Entities"},
     * security={{"bearer_token": {} }},
     *   @OA\RequestBody(
     *       required=true,
     *       @OA\MediaType(
     *           mediaType="multipart/form-data",
     *           @OA\Schema(
     *              required={"name[ar]"},
     *              @OA\Property(property="name[en]", type="string"),
     *              @OA\Property(property="name[ar]", type="string"),
     *              @OA\Property(property="address", type="string"),
     *              @OA\Property(property="email", type="string"),
     *              @OA\Property(property="contact_number", type="string"),
     *              @OA\Property(property="founding_date", type="string"),
     *              @OA\Property(property="price_per_pet", type="float"),
     *              @OA\Property(property="allowed_branches", type="integer"),
     *              @OA\Property(property="allowed_users", type="integer"),
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
            'name'              => ['required', 'array', translation_rule()],
            'address'           => ['required', 'string'],
            'email'             => ['required', 'string'],
            'contact_number'    => ['required', 'string'],
            'founding_date'     => ['required', 'date_format:Y-m-d'],
            'price_per_pet'     => ['required', 'flaot'],
            'allowed_branches'  => ['required', 'integer'],
            'allowed_users'     => ['required', 'integer'],
        ]);
     
        $entity = Entity::create([
            'name'              => $request->name,
            'address'           => $request->address,
            'email'             => $request->email,
            'contact_number'    => $request->contact_number,
            'founding_date'     => $request->founding_date,
            'price_per_pet'     => $request->price_per_pet,
            'allowed_branches'  => $request->allowed_branches,
            'allowed_users'     => $request->allowed_users,
        ]);

        $user = User::create([
            'entity_id'          => $entity->id,
            'name'               => $request->name,
            'email'              => $request->email,
            'phone'              => $request->contact_number,
            'password'           => Hash::make($request->name.'@123'),
        ]);

        return response()->json(new UserResource($user), 200);
    }

    /**
     * @OA\Get(
     * path="/admin/entities/{id}",
     * description="Get entity information.",
     *     @OA\Parameter(
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(type="string"),
     *      ),
     * operationId="show_entity",
     * tags={"Admin - Entities"},
     * security={{"bearer_token": {} }},
     * @OA\Response(
     *    response=200,
     *    description="successful operation"
     * ),
     * )
     *)
    */
    public function show(Entity $entity)
    {
        $entity->load(['branches']);
        return response()->json(new EntityResource($entity), 200);
    }

    /**
     * @OA\Post(
     * path="/admin/entities/{id}",
     * description="Edit entity.",
     *   @OA\Parameter(
     *     in="path",
     *     name="id",
     *     required=true,
     *     @OA\Schema(type="string"),
     *   ),
     *  tags={"Admin - Entities"},
     *  security={{"bearer_token": {} }},
     *   @OA\RequestBody(
     *       required=true,
     *       @OA\MediaType(
     *           mediaType="multipart/form-data",
     *           @OA\Schema(
     *              @OA\Property(property="name[en]", type="string"),
     *              @OA\Property(property="name[ar]", type="string"),
     *              @OA\Property(property="address", type="string"),
     *              @OA\Property(property="email", type="string"),
     *              @OA\Property(property="contact_number", type="string"),
     *              @OA\Property(property="founding_date", type="string"),
     *              @OA\Property(property="price_per_pet", type="string"),
     *              @OA\Property(property="allowed_branches", type="string"),
     *              @OA\Property(property="allowed_users", type="string"),
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
    public function update(Request $request, Entity $entity)
    {
        $request->validate([
            'name'              => ['required', 'array', translation_rule()],
            'address'           => ['required', 'string'],
            'email'             => ['required', 'string'],
            'contact_number'    => ['required', 'string'],
            'founding_date'     => ['required', 'date_format:Y-m-d'],
            'price_per_pet'     => ['required', 'flaot'],
            'allowed_branches'  => ['required', 'integer'],
            'allowed_users'     => ['required', 'integer'],
        ]);

        $entity->update([
            'name'         => $request->name,
            'address'           => $request->address,
            'email'             => $request->email,
            'contact_number'    => $request->contact_number,
            'founding_date'     => $request->founding_date,
            'price_per_pet'     => $request->price_per_pet,
            'allowed_branches'  => $request->allowed_branches,
            'allowed_users'     => $request->allowed_users,
        ]);

        return response()->json(new EntityResource($entity), 200);
    }

    /**
     * @OA\Delete(
     * path="/admin/entities/{id}",
     * description="Delete entered Entity.",
     *     @OA\Parameter(
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(type="string"),
     *         @OA\Examples(example="int", value="1", summary="An int value."),
     *      ),
     * operationId="delete_entity",
     * tags={"Admin - Entities"},
     * security={{"bearer_token":{}}},
     * @OA\Response(
     *    response=200,
     *    description="successful operation"
     * ),
     * )
     *)
    */
    public function destroy(Entity $entity)
    {
        $entity->delete();
        return response()->json(null, 204);
    }
}
