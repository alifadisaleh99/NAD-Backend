<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Animal;
use App\Models\Category;
use App\Models\Entity;
use App\Models\plan;
use Carbon\Carbon;
use Illuminate\Http\Request;

class StatisticsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->middleware('permission:statistics.read');
    }

            /**
         * @OA\Get(
         *   path="/admin/statistics/overview",
         *   description="Get statistics for categories, plans, and animals",
         *   operationId="getStatisticsOverview",
         *   tags={"Admin - Statistics"},
         *   security={{"bearer_token": {}}},
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
         *     name="per_page",
         *     in="query",
         *     description="Number of items per page",
         *     required=false,
         *     @OA\Schema(
         *       type="integer"
         *     )
         *   ),
         *   @OA\Response(
         *     response=200,
         *     description="Success",
         *   )
         * )
         */

    public function getCategoryPlanAnimalStatistics(Request $request)
    {
        $request->validate([
            'with_paginate' => ['integer', 'in:0,1'],
            'per_page'      => ['integer', 'min:1'],
            'start_date'          => ['date_format:Y-m-d'],
            'end_date'            => ['date_format:Y-m-d']
        ]);

        $q = Category::with('animals');

        if ($request->with_paginate === '0')
            $categories = $q->with('animals')->get();
        else
            $categories = $q->with('animals')->paginate($request->per_page ?? 10);

        $categories = $categories->map(function ($category) {
            return [
                'category_id' => $category->id,
                'category_name' => $category->name,
                'animals_count' => $category->animals->count(),
                'name_translations'  => $category->translations['name'],
            ];
        });

        $sub7days = Carbon::now()->subDays(7);
        $count_animal_created_last_7_days = Animal::where('created_at', '>=', $sub7days)->count();


        $plans = Plan::with('users')->get()->map(function ($plan) {
            $count_users = $plan->users->count();
            return [
                'plan_id' => $plan->id,
                'plan_name' => $plan->name,
                'users_count' => $count_users,
                'plan_earnings' => $plan->price * $count_users,
                'name_translations'  => $plan->translations['name'],
            ];
        });

        return response()->json([
            'categories' => $categories,
            'plans' => $plans,
            'count_animals_created_last_7_days' => $count_animal_created_last_7_days,
        ]);
    }

            /**
         * @OA\Get(
         *   path="/admin/statistics/entity-earnings",
         *   description="get statistics for entity earnings",
         *   tags={"Admin - Statistics"},
         *   security={{"bearer_token": {}}},
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
         *     name="per_page",
         *     in="query",
         *     description="Number of items per page",
         *     required=false,
         *     @OA\Schema(
         *       type="integer"
         *     )
         *   ),
         *   @OA\Response(
         *     response=200,
         *     description="Success",
         *   )
         * )
         */

    public function getEntityEarnings(Request $request)
    {
        $request->validate([
            'with_paginate' => ['integer', 'in:0,1'],
            'per_page'      => ['integer', 'min:1'],
            'start_date'          => ['date_format:Y-m-d'],
            'end_date'            => ['date_format:Y-m-d']
        ]);

        $q = Entity::with('animals');

        if ($request->start_date)
            $q->where('created_at', '>=', $request->start_date);
        if ($request->end_date)
            $q->where('created_at', '<=', $request->end_date);

        if ($request->with_paginate === '0')
            $entites = $q->with('animals')->get();
        else
            $entites = $q->with('animals')->paginate($request->per_page ?? 10);

        $entites = $entites->map(function ($entity) {
            $count_animals = $entity->animals->count();
            return [
                'entity_id' => $entity->id,
                'entaity_name' => $entity->name,
                'animals_count' => $count_animals,
                'entity_earnings' => $count_animals * $entity->price_per_pet,
                'name_translations'  => $entity->translations['name'],
            ];
        });

        return response()->json([
            'entites' => $entites,
        ]);
    }
}
