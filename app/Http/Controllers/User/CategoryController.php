<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\GetRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Services\CategoryService;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public  $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->middleware('auth:sanctum');

        $this->categoryService = $categoryService;
    }

    /**
     * @OA\Get(
     * path="/user/categories",
     * description="Get all categories",
     * operationId="get_all_categories_for_me",
     * tags={"User - Categories"},
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
     * @OA\Get(
     * path="/user/categories/{id}",
     * description="Get category information for user.",
     *     @OA\Parameter(
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(type="string"),
     *      ),
     * operationId="show_category_for_me",
     * tags={"User - Categories"},
     *   security={{"bearer_token": {} }},
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
}
