<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\GetRequest;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\RoleResource;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->middleware('permission:roles.read|roles.write|roles.delete|users.read|users.write')->only(['index', 'show']);
        $this->middleware('permission:roles.write')->only(['store', 'update']);
        $this->middleware('permission:roles.delete')->only(['destroy']);
    }

    /**
     * @OA\Get(
     *   path="/admin/roles",
     *   description="get all roles",
     *   tags={"Roles"},
     *   security={{"bearer_token": {} }},
     *   @OA\Parameter(
     *     name="with_paginate",
     *     in="query",
     *     description="Enable pagination (0 = false, 1 = true)",
     *     required=false,
     *     @OA\Schema(
     *       type="integer",
     *       enum={0, 1}
     *     )
     *   ),
     *   @OA\Parameter(
     *     in="query",
     *     name="per_page",
     *     required=false,
     *     @OA\Schema(type="integer"),
     *   ),
     *   @OA\Parameter(
     *     in="query",
     *     name="search",
     *     required=false,
     *     @OA\Schema(type="string"),
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Success",
     *   ),
     *   @OA\Response(
     *     response=401,
     *     description="Unauthenticated",
     *   ),
     *   @OA\Response(
     *     response=403,
     *     description="Forbidden. This action is unauthorized.",
     *   )
     * )
     */

    public function index(GetRequest $request)
    {
        $searchTerm = $request->query('search');
        $query = Role::query();

        if ($searchTerm) {
            $query->where(function($query) use ($searchTerm) {
                $query->where('name_en', 'like', '%' . $searchTerm . '%')->orWhere('name_ar', 'like', '%' . $searchTerm . '%');
            });
        }

        if ($request->with_paginate === '0')
            $roles = $query->with('permissions')->get();
        else
            $roles = $query->with('permissions')->paginate($request->per_page ?? 10);

        return RoleResource::collection($roles);
    }

    /**
     * @OA\Post(
     *   path="/admin/roles",
     *   description="Create a new role",
     *   tags={"Roles"},
     *   security={{"bearer_token": {} }},
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\MediaType(
     *       mediaType="multipart/form-data",
     *       @OA\Schema(
     *         required={"name_en", "name_ar"},
     *              @OA\Property(property="name_en", type="string"),
     *              @OA\Property(property="name_ar", type="string"),
     *       )
     *     )
     *   ),
     *   @OA\Response(
     *     response=201,
     *     description="Created",
     *   ),
     *   @OA\Response(
     *     response=401,
     *     description="Unauthenticated",
     *   ),
     *   @OA\Response(
     *     response=422,
     *     description="Validation error",
     *   )
     * )
     */

    public function store(Request $request)
    {
        $request->validate([
            'name_en' => ['required', 'string'],
            'name_ar' => ['required', 'string'],
        ]);

        $role = Role::create([
            'name' => $request->name_ar,
            'name_en' => $request->name_en,
            'name_ar' => $request->name_ar,
            'guard_name' => 'web'
        ]);

        return response()->json(new RoleResource($role), 201);
    }

    /**
     * @OA\Post(
     *   path="/admin/roles/{role}",
     *   description="Edit specific role",
     *   @OA\Parameter(
     *     in="path",
     *     name="role",
     *     required=true,
     *     @OA\Schema(type="string"),
     *   ),
     *   tags={"Roles"},
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\MediaType(
     *       mediaType="multipart/form-data",
     *       @OA\Schema(
     *         required={"name_en", "name_ar"},
     *              @OA\Property(property="name_en", type="string"),
     *              @OA\Property(property="name_ar", type="string"),
     *              @OA\Property(property="_method", type="string", format="string", example="PUT"),
     *       )
     *     )
     *   ),
     *   security={{"bearer_token":{}}},
     *   @OA\Response(
     *     response="200",
     *     description="Success"
     *   ),
     *   @OA\Response(
     *     response=401,
     *     description="Unauthenticated",
     *   ),
     *   @OA\Response(
     *     response=422,
     *     description="Validation error",
     *   ),
     *   @OA\Response(
     *     response=403,
     *     description="Forbidden. This action is unauthorized.",
     *   )
     * )
     */

    public function update(Request $request, Role $role)
    {
        if ($role->name_en == 'admin' || $role->name_en == 'user' || $role->name_en == 'poet')
                 throw new BadRequestHttpException(__('error_messages.Sorry, You can not update this role'));

        $request->validate([
            'name_en' => ['required', 'string'],
            'name_ar' => ['required', 'string'],
        ]);

        $role = Role::create([
            'name' => $request->name_ar,
            'name_en' => $request->name_en,
            'name_ar' => $request->name_ar,
            'guard_name' => 'web'
        ]);

        return response()->json(new RoleResource($role), 200);
    }

    /**
     * @OA\Get(
     *   path="/admin/roles/{role}",
     *   description="Get a role by ID",
     *   tags={"Roles"},
     *   security={{"bearer_token": {} }},
     *   @OA\Parameter(
     *     name="role",
     *     in="path",
     *     description="Role ID",
     *     required=true,
     *     @OA\Schema(
     *       type="integer",
     *       format="int64"
     *     )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Success",
     *   ),
     *   @OA\Response(
     *     response=401,
     *     description="Unauthenticated",
     *   ),
     *   @OA\Response(
     *     response=404,
     *     description="Role not found",
     *   )
     * )
     */

    public function show(Role $role)
    {
        $role->load('permissions');
        return response()->json(new RoleResource($role), 200);
    }

    /** 
     * @OA\Delete(
     *   path="/admin/roles/{role}",
     *   description="Delete a role by ID",
     *   tags={"Roles"},
     *   security={{"bearer_token": {} }},
     *   @OA\Parameter(
     *     name="role",
     *     in="path",
     *     description="Role ID",
     *     required=true,
     *     @OA\Schema(
     *       type="integer",
     *       format="int64"
     *     )
     *   ),
     *   @OA\Response(
     *     response=204,
     *     description="No Content",
     *   ),
     *   @OA\Response(
     *     response=401,
     *     description="Unauthenticated",
     *   ),
     *   @OA\Response(
     *     response=404,
     *     description="Role not found",
     *   )
     * )
     */

    public function destroy(Role $role)
    {
        if ($role->name_en == 'admin' || $role->name_en == 'user' || $role->name_en == 'poet')
            throw new BadRequestHttpException(__('error_messages.Sorry, You can not delete this role'));

        $role_employees = DB::table('model_has_roles')->where('role_id', $role->id)->exists();
        if ($role_employees)
            throw new BadRequestHttpException(__('error_messages.Sorry, You can not delete this role'));

        $role->delete();

        return response()->json(null, 204);
    }
}
