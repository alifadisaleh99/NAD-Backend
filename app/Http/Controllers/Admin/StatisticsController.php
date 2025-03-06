<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\PlanEarningsResource;
use App\Http\Resources\SubscriptionResource;
use App\Models\Animal;
use App\Models\Category;
use App\Models\Entity;
use App\Models\plan;
use App\Models\Subscription;
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
     *   path="/adminstatistics/count-animals",
     *   description="Get statistics for count animals by dates",
     *   operationId="getStatisticsAnimalsCount",
     *   tags={"Admin - Statistics"},
     *   security={{"bearer_token": {}}},
     *   @OA\Response(
     *     response=200,
     *     description="Success",
     *   )
     * )
     */

    public function getAnimalsCount(Request $request)
    { 
        $current_week = [];
        $last_week = [];

        for ($i = 0; $i < 7; $i++) {
            $c_day = Carbon::today()->subDays($i);
            $count_animals = Animal::whereDate('created_at', $c_day)->count();
            $current_week[$c_day->format('l')] =  $count_animals;
        }

        for ($i = 0; $i < 7; $i++) {
            $l_day = today()->subDays(7 + $i);
            $count_animals = Animal::whereDate('created_at', $l_day)->count();
            $last_week[$l_day->format('l')] =  $count_animals;
        }

        $start_current_week = Carbon::today()->startOfWeek(Carbon::SATURDAY);
        $start_current_month = Carbon::today()->startOfMonth();
        $start_last_month  = Carbon::today()->subMonth()->startOfMonth();
        $end_last_month  = Carbon::today()->subMonth()->endOfMonth();

        $count_animals_current_week  = Animal::whereDate('created_at', '>=', $start_current_week)->count();
        $count_animals_current_month  = Animal::whereDate('created_at', '>=', $start_current_month)->count();
        $count_animals_last_month     = Animal::whereBetween('created_at', [$start_last_month, $end_last_month])->count();


        return response()->json([
            'current_week' => $current_week,
            'last_week'    => $last_week,
            'count_animals_current_week'  => $count_animals_current_week,
            'count_animals_current_month' => $count_animals_current_month,
            'count_animals_last_month'    => $count_animals_last_month,
        ]);
    }

    /**
     * @OA\Get(
     *   path="/admin/statistics/plan-earnings",
     *   description="get statistics for plan earnings",
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

    public function getPlanEarnings(Request $request)
    {
        $q = plan::latest();

        if ($request->with_paginate === '0')
            $plans = $q->get();
        else
            $plans = $q->paginate($request->per_page ?? 10);

        return PlanEarningsResource::collection($plans);
    }

    /**
     * @OA\Get(
     *   path="/admin/statistics/subscriptions",
     *   description="get statistics: all subscriptions",
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
     *     type="integer"
     *     )
     *   ),
     * @OA\Parameter(
     *    in="query",
     *    name="q",
     *    required=false,
     *    @OA\Schema(type="string"),
     * ),
     *   @OA\Response(
     *     response=200,
     *     description="Success",
     *   )
     * )
     */

    public function getsubscriptions(Request $request)
    {
        $request->validate([
            'with_paginate'      => ['integer', 'in:0,1'],
            'per_page'           => ['integer', 'min:1'],
            'q'                  => ['string']
        ]);

        $q = Subscription::with('plan')->latest();

        if ($request->q) {
            $subscriptions_ids = $q->where('user_name', 'LIKE', '%' . $request->q . '%')->pluck('id');

            $q->where(function($query) use ($request,  $subscriptions_ids) {
                if (is_numeric($request->q))
                     $query->where('id', $request->q);
                
               $query->orWhereIn('id',  $subscriptions_ids);
            });
        }      

        if ($request->with_paginate === '0')
            $subscriptions = $q->get();
        else
            $subscriptions  = $q->paginate($request->per_page ?? 10);

            return SubscriptionResource::collection($subscriptions);
    }
}
