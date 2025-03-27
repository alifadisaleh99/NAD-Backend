<?php

namespace App\Http\Controllers;

use App\Http\Requests\GetRequest;
use App\Http\Resources\CountryResource;
use App\Models\Country;
use Illuminate\Http\Request;
use Mosab\Translation\Models\Translation;

class CountryController extends Controller
{
    /**
     * @OA\Get(
     * path="/countries",
     * description="Get all countries",
     * operationId="get_all_countries_to_user",
     * tags={"User - Countries"},
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
     *     in="query",
     *     name="country_code",
     *     required=false,
     *     @OA\Schema(type="string")
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
     *  )
     *  )
     */
    public function index(GetRequest $request)
    {
        $q = Country::query()->latest();

        if($request->country_code)
            $q->where('country_code', $request->country_code);
        
        if ($request->q) {
            $countries_ids = Translation::where('translatable_type', Country::class)
                ->where('attribute', 'name')
                ->where('value', 'LIKE', '%' . $request->q . '%')
                ->groupBy('translatable_id')
                ->pluck('translatable_id');

            $q->where(function ($query) use ($request, $countries_ids) {
                if (is_numeric($request->q))
                    $query->where('id', $request->q);

                $query->orWhereIn('id', $countries_ids);
            });
        }

        if ($request->with_paginate === '0')
            $countries = $q->get();
        else
            $countries = $q->paginate($request->per_page ?? 10);

        return CountryResource::collection($countries);
    }
    /**
     * @OA\Get(
     * path="/countries/{id}",
     * description="Get Country information.",
     *     @OA\Parameter(
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(type="string"),
     *      ),
     * operationId="show_country_to_user",
     * tags={"User - Countries"},
     * @OA\Response(
     *    response=200,
     *    description="successful operation"
     * ),
     * )
     *)
     */
    public function show(Country $country)
    {
        return response()->json(new CountryResource($country), 200);
    }
}
