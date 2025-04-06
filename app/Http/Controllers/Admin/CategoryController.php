<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Http\Requests\GetRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Services\CategoryService;

class CategoryController extends Controller
{
    public  $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->middleware('auth:sanctum');
        $this->middleware('permission:categories.read|categories.write|categories.delete')->only('index', 'show');
        $this->middleware('permission:categories.write')->only('store', 'update');
        $this->middleware('permission:categories.delete')->only('destroy');

        $this->categoryService = $categoryService;
    }

    /**
     * @OA\Get(
     * path="/admin/categories",
     * description="Get all categories",
     * operationId="get_all_categories",
     * tags={"Admin - Categories"},
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
        $categories = $this->categoryService->getAllCategories($request);

        return CategoryResource::collection($categories);
    }

    /**
     * @OA\Post(
     * path="/admin/categories",
     * description="Add new category.",
     * tags={"Admin - Categories"},
     * security={{"bearer_token": {} }},
     *   @OA\RequestBody(
     *       required=true,
     *       @OA\MediaType(
     *           mediaType="multipart/form-data",
     *           @OA\Schema(
     *              required={"name[ar]"},
     *              @OA\Property(property="name[en]", type="string"),
     *              @OA\Property(property="name[ar]", type="string"),
     *              @OA\Property(property="description[en]", type="string"),
     *              @OA\Property(property="description[ar]", type="string"),
     *              @OA\Property(property="image", type="file"),
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
    public function store(CategoryRequest $request)
    {
        $category = $this->categoryService->create($request);

        return response()->json(new CategoryResource($category), 200);
    }

    /**
     * @OA\Get(
     * path="/admin/categories/{id}",
     * description="Get category information.",
     *     @OA\Parameter(
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(type="string"),
     *      ),
     * operationId="show_category",
     * tags={"Admin - Categories"},
     * security={{"bearer_token": {} }},
     * @OA\Response(
     *    response=200,
     *    description="successful operation"
     * ),
     * )
     *)
    */
    public function show(Category $category)
    {
        $category->load('animals');
        
        return response()->json(new CategoryResource($category), 200);
    }

    /**
     * @OA\Post(
     * path="/admin/categories/{id}",
     * description="Edit category.",
     *   @OA\Parameter(
     *     in="path",
     *     name="id",
     *     required=true,
     *     @OA\Schema(type="string"),
     *   ),
     *  tags={"Admin - Categories"},
     *  security={{"bearer_token": {} }},
     *   @OA\RequestBody(
     *       required=true,
     *       @OA\MediaType(
     *           mediaType="multipart/form-data",
     *           @OA\Schema(
     *              @OA\Property(property="name[en]", type="string"),
     *              @OA\Property(property="name[ar]", type="string"),
     *              @OA\Property(property="description[en]", type="string"),
     *              @OA\Property(property="description[ar]", type="string"),
     *              @OA\Property(property="image", type="file"),
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
    public function update(CategoryRequest $request, Category $category)
    {
        $this->categoryService->update($request, $category);

        return response()->json(new CategoryResource($category), 200);
    }

    /**
     * @OA\Delete(
     * path="/admin/categories/{id}",
     * description="Delete entered category.",
     *     @OA\Parameter(
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(type="string"),
     *         @OA\Examples(example="int", value="1", summary="An int value."),
     *      ),
     * operationId="delete_category",
     * tags={"Admin - Categories"},
     * security={{"bearer_token":{}}},
     * @OA\Response(
     *    response=204,
     *    description="successful operation"
     * ),
     * )
     *)
    */
    public function destroy(Category $category)
    {
        $category->delete();
        
        return response()->json(null, 204); 
    }
}
