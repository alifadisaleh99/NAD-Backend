<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\PlanResource;
use App\Models\plan;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Mosab\Translation\Models\Translation;

class PlanController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->middleware('permission:plans.read|plans.write|plans.delete')->only('index', 'show');
        $this->middleware('permission:plans.write')->only('store', 'update');
        $this->middleware('permission:plans.delete')->only('destroy');
    }

    /**
     * @OA\Get(
     * path="/admin/plans",
     * description="Get all plans",
     * operationId="get_all_plans",
     * tags={"Admin - Plans"},
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
     * @OA\Response(
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
            'q'                   => ['string'],
        ]);

        $q = plan::query()->latest();

        if($request->q)
        {
            $plans_ids = Translation::where('translatable_type', Plan::class)
                                        ->where('attribute', 'name')
                                        ->where('value', 'LIKE', '%'.$request->q.'%')
                                        ->groupBy('translatable_id')
                                        ->pluck('translatable_id');

            $q->where(function($query) use ($request, $plans_ids) {
                if (is_numeric($request->q))
                    $query->where('id', $request->q);
        
                $query->orWhereIn('id', $plans_ids);
            });
        }

        if ($request->with_paginate === '0')
            $plans = $q->get();
        else
            $plans = $q->paginate($request->per_page ?? 10);

        return PlanResource::collection($plans);
    }

    /**
     * @OA\Post(
     * path="/admin/plans",
     * description="Add new plan.",
     * tags={"Admin - Plans"},
     * security={{"bearer_token": {} }},
     *   @OA\RequestBody(
     *       required=true,
     *       @OA\MediaType(
     *           mediaType="multipart/form-data",
     *           @OA\Schema(
     *            required={"price", "status", "addition_count","name[ar]"},
     *              @OA\Property(property="name[en]", type="string"),
     *              @OA\Property(property="name[ar]", type="string"),
     *              @OA\Property(property="price", type="number", format="float", example=25.50,),
     *              @OA\Property(property="status", type="boolean", enum={0, 1}),
     *              @OA\Property(property="addition_count", type="integer"),
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
    public function store(Request $request)
    {
        $request->validate([
            'name'      => ['required', 'array', translation_rule()],
            'price'     => ['required', 'numeric', 'min:0'],
            'status'    => ['required', 'in:1,0'],
            'addition_count' => ['required', 'integer'],
            'image'     => ['image'],
        ]);


        $plan = plan::create([
            'name'          => $request->name,
            'price'          => $request->price,
            'status'         => $request->status,
            'addition_count' => $request->addition_count,
        ]);

        if($request->image)
         { 
            $image = upload_file($request->image, 'plans', 'plan');
            $plan->image = $image;
            $plan->save();

        }

        return response()->json(new PlanResource($plan), 200);
    }

    /**
     * @OA\Get(
     * path="/admin/plans/{id}",
     * description="Get plan information.",
     *     @OA\Parameter(
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(type="string"),
     *      ),
     * operationId="show_plan",
     * tags={"Admin - Plans"},
     * security={{"bearer_token": {} }},
     * @OA\Response(
     *    response=200,
     *    description="successful operation"
     * ),
     * )
     *)
     */
    public function show(Plan $plan)
    {
        return response()->json(new PlanResource($plan), 200);
    }

    /**
     * @OA\Post(
     * path="/admin/plans/{id}",
     * description="Edit plan.",
     *   @OA\Parameter(
     *     in="path",
     *     name="id",
     *     required=true,
     *     @OA\Schema(type="string"),
     *   ),
     *  tags={"Admin - Plans"},
     *  security={{"bearer_token": {} }},
     *   @OA\RequestBody(
     *       required=true,
     *       @OA\MediaType(
     *           mediaType="multipart/form-data",
     *           @OA\Schema(
     *              required={"price", "status", "addition_count", "name[ar]"},
     *              @OA\Property(property="name[en]", type="string"),
     *              @OA\Property(property="name[ar]", type="string"),
     *              @OA\Property(property="price", type="number", format="float", example=25.50,),
     *              @OA\Property(property="status", type="boolean", enum={0, 1}),
     *              @OA\Property(property="addition_count", type="integer"),
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
    public function update(Request $request, Plan $plan)
    {
        $request->validate([
            'name'      => ['required', 'array', translation_rule()],
            'price'     => ['required', 'numeric', 'min:0'],
            'status'    => ['required', 'in:1,0'],
            'addition_count' => ['required', 'integer'],
            'image'             => [''],
        ]);

        $image = null;
        if ($request->image) {
            if ($request->image == $plan->image) {
                $image = $plan->image;
            } else {
                if (!is_file($request->image))
                    throw ValidationException::withMessages(['image' => __('error_messages.Image should be a file')]);
                $image = upload_file($request->image, 'plans', 'plan');
            }
        }

        $plan->update([
            'name'           => $request->name,
            'price'          => $request->price,
            'status'   => $request->status,
            'addition_count' => $request->addition_count,
            'image'         => $image,
        ]);

        return response()->json(new PlanResource($plan), 200);
    }

    /**
     * @OA\Delete(
     * path="/admin/plans/{id}",
     * description="Delete entered plan.",
     *     @OA\Parameter(
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(type="string"),
     *         @OA\Examples(example="int", value="1", summary="An int value."),
     *      ),
     * operationId="delete_plan",
     * tags={"Admin - Plans"},
     * security={{"bearer_token":{}}},
     * @OA\Response(
     *    response=200,
     *    description="successful operation"
     * ),
     * )
     *)
     */
    public function destroy(Plan $plan)
    {
        delete_file_if_exist($plan->image);
        $plan->delete();
        return response()->json(null, 204);
    }
}
