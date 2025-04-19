<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\LostReportRequest;
use App\Http\Requests\GetRequest;
use App\Http\Requests\TransferRequest;
use App\Http\Requests\UserStoreAnimalRequest;
use App\Http\Requests\UserUpdateAnimalRequest;
use App\Http\Resources\AnimalResource;
use App\Http\Resources\OwnershipRecordResource;
use App\Models\Animal;
use App\Services\AnimalService;


class AnimalController extends Controller
{
    public  $animalService;

    public function __construct(AnimalService $animalService)
    {
        $this->middleware('auth:sanctum');
        //   $this->middleware('permission:animals.transfer')->only('generateTransferToken', 'acceptTransfer');
        //  $this->middleware('permission:ownershipRecords.read')->only('ownershipRecords');
        $this->middleware('owner.animal')->only(['index', 'show', 'store', 'update', 'destroy', 'ownershipRecords', 'generateTransferToken']);

        $this->animalService = $animalService;
    }

    /**
     * @OA\Get(
     * path="/user/animals",
     * description="Get all animals",
     * operationId="get_all_animals_for_me",
     * tags={"User - Animals"},
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
     *    name="ownership_date",
     *    required=false,
     *    @OA\Schema(type="integer", enum={"1", "0"}),
     * ),
     * @OA\Parameter(
     *    in="query",
     *    name="category_id",
     *    required=false,
     *    @OA\Schema(type="integer"),
     * ),

     * @OA\Parameter(
     *    in="query",
     *    name="animal_specie_id",
     *    required=false,
     *    @OA\Schema(type="integer"),
     * ),
     * @OA\Parameter(
     *    in="query",
     *    name="animal_breed_id",
     *    required=false,
     *    @OA\Schema(type="integer"),
     * ),
     * @OA\Parameter(
     *    in="query",
     *    name="uaid",
     *    required=false,
     *    @OA\Schema(type="string"),
     * ),
     * @OA\Parameter(
     *    in="query",
     *    name="tag_number",
     *    required=false,
     *    @OA\Schema(type="string"),
     * ),
     * @OA\Parameter(
     *    in="query",
     *    name="pet_status",
     *    required=false,
     *    @OA\Schema(type="string", enum={"lost", "found", "dead"}),
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
        $animals = $this->animalService->getAllAnimals($request, true);
        
        return AnimalResource::collection($animals);
    }

    /**
     * @OA\Post(
     * path="/user/animals",
     * description="Add new animal.",
     * tags={"User - Animals"},
     * security={{"bearer_token": {}}},
     * @OA\RequestBody(
     *       required=true,
     *       @OA\MediaType(
     *           mediaType="multipart/form-data",
     *           @OA\Schema(
     *              required={"name[ar]", "category_id", "primary_color_id", "birth_date", "gender", "status"},
     *              @OA\Property(property="branch_id", type="integer"),
     *              @OA\Property(property="name[en]", type="string"),
     *              @OA\Property(property="name[ar]", type="string"),
     *              @OA\Property(property="description[en]", type="string"),
     *              @OA\Property(property="description[ar]", type="string"),
     *              @OA\Property(property="like[en]", type="string"),
     *              @OA\Property(property="like[ar]", type="string"),
     *              @OA\Property(property="deslike[en]", type="string"),
     *              @OA\Property(property="deslike[ar]", type="string"),
     *              @OA\Property(property="good_with[en]", type="string"),
     *              @OA\Property(property="good_with[ar]", type="string"),
     *              @OA\Property(property="bad_with[en]", type="string"),
     *              @OA\Property(property="bad_with[ar]", type="string"),
     *              @OA\Property(property="sensitivities[0]", type="string"),
     *              @OA\Property(property="link", type="string"),
     *              @OA\Property(property="status", type="boolean", enum={0, 1}),
     *              @OA\Property(property="photos[0]", type="file"),
     *              @OA\Property(property="category_id", type="integer"),
     *              @OA\Property(property="animal_specie_id", type="integer"),
     *              @OA\Property(property="animal_breed_id", type="integer"),
     *              @OA\Property(property="pet_mark_ids[0]", type="integer"),
     *              @OA\Property(property="attachments[0][name]", type="string"),
     *              @OA\Property(property="attachments[0][source]", type="string"),
     *              @OA\Property(property="attachments[0][attachment_date]", type="date"),
     *              @OA\Property(property="attachments[0][file]", type="file"),
     *              @OA\Property(property="vaccinations[0][name]", type="string"),
     *              @OA\Property(property="vaccinations[0][vaccination_date]", type="date"),
     *              @OA\Property(property="vaccinations[0][duration]", type="integer"),
     *              @OA\Property(property="vaccinations[0][is_expired]", type="integer", enum={"1", "0"}),
     *              @OA\Property(property="tags[0][tag_type_id]", type="integer"),
     *              @OA\Property(property="tags[0][factory_number]", type="string"),
     *              @OA\Property(property="tags[0][number]", type="string"),
     *              @OA\Property(property="tags[0][status]", type="integer", enum={"0"," 1"}),
     *              @OA\Property(property="primary_color_id", type="integer"),
     *              @OA\Property(property="secondary_color_id", type="integer"),
     *              @OA\Property(property="tertiary_color_id", type="integer"),
     *              @OA\Property(property="age", type="string", enum={"young", "adult", "senior"}),
     *              @OA\Property(property="birth_date", type="date"),
     *              @OA\Property(property="weight", type="float"),
     *              @OA\Property(property="gender", type="string", enum={"male", "female"}),
     *              @OA\Property(property="size", type="string", enum={"small", "medium", "large"}),
     *              @OA\Property(property="digital_link", type="string"),
     *              @OA\Property(property="generate_public", type="boolean", enum={0, 1}),
     *              @OA\Property(property="cover_image", type="file"),
     *              @OA\Property(property="file_image", type="file"),
     *              @OA\Property(property="ownership_date", type="date"),
     *          )
     *       )
     * ),
     * @OA\Response(
     *    response=200,
     *    description="successful operation",
     * ),
     * )
     */
    public function store(UserStoreAnimalRequest $request)
    {
        /*  if ($request->user_id) {
            $user = User::with(['animals'])->find($request->user_id);
            $subscription = $user->subscriptions()->where('is_active', 1)->first();
      

            if ($user && $subscription) {
                $currentAnimalsCount = $user->animals->count();

                $maxAllowedAnimals = $subscription->plan->addition_count;

                if ($currentAnimalsCount >= $maxAllowedAnimals)
                    return response()->json(['message' => __('error_messages.max_animals_reached')], 422);
            }
            else
                return response()->json(['message' => __('error_messages.user_must_have_plan')], 422);
        }
        */
        
        $animal = $this->animalService->create($request, true);

        return response()->json(new AnimalResource($animal), 200);
    }

    /**
     * @OA\Get(
     * path="/user/animals/{id}",
     * description="Get animal information.",
     *     @OA\Parameter(
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(type="string"),
     *      ),
     * operationId="show_animal_for_me",
     * tags={"User - Animals"},
     * security={{"bearer_token": {} }},
     * @OA\Response(
     *    response=200,
     *    description="successful operation"
     * ),
     * )
     *)
     */
    public function show(Animal $animal)
    {
        $this->animalService->show($animal);

        return response()->json(new AnimalResource($animal), 200);
    }

    /**
     * @OA\Post(
     * path="/user/animals/{id}",
     * description="Edit animal.",
     * @OA\Parameter(
     *     in="path",
     *     name="id",
     *     required=true,
     *     @OA\Schema(type="string"),
     * ),
     * tags={"User - Animals"},
     * security={{"bearer_token": {}}},
     * @OA\RequestBody(
     *       required=true,
     *       @OA\MediaType(
     *           mediaType="multipart/form-data",
     *           @OA\Schema(
     *              @OA\Property(property="branch_id", type="integer"),
     *              @OA\Property(property="name[en]", type="string"),
     *              @OA\Property(property="name[ar]", type="string"),
     *              @OA\Property(property="description[en]", type="string"),
     *              @OA\Property(property="description[ar]", type="string"),
     *              @OA\Property(property="like[en]", type="string"),
     *              @OA\Property(property="like[ar]", type="string"),
     *              @OA\Property(property="deslike[en]", type="string"),
     *              @OA\Property(property="deslike[ar]", type="string"),
     *              @OA\Property(property="good_with[en]", type="string"),
     *              @OA\Property(property="good_with[ar]", type="string"),
     *              @OA\Property(property="bad_with[en]", type="string"),
     *              @OA\Property(property="bad_with[ar]", type="string"),
     *              @OA\Property(property="deleted_sensitivity_ids[0]", type="integer"),
     *              @OA\Property(property="sensitivities[0]", type="string"),
     *              @OA\Property(property="link", type="string"),
     *              @OA\Property(property="status", type="boolean", enum={0, 1}),
     *              @OA\Property(property="photos[0]", type="file"),
     *              @OA\Property(property="deleted_media_ids[0]", type="integer"),
     *              @OA\Property(property="category_id", type="integer"),
     *              @OA\Property(property="animal_specie_id", type="integer"),
     *              @OA\Property(property="animal_breed_id", type="integer"),
     *              @OA\Property(property="pet_mark_ids[0]", type="integer"),
     *              @OA\Property(property="deleted_pet_mark_ids[0]", type="integer"),
     *              @OA\Property(property="deleted_attachment_ids[0]", type="integer"),
     *              @OA\Property(property="attachments[0][id]", type="integer"),
     *              @OA\Property(property="attachments[0][name]", type="string"),
     *              @OA\Property(property="attachments[0][source]", type="string"),
     *              @OA\Property(property="attachments[0][attachment_date]", type="date"),
     *              @OA\Property(property="attachments[0][file]", type="file"),
     *              @OA\Property(property="deleted_vaccination_ids[0]", type="integer"),
     *              @OA\Property(property="vaccinations[0][id]", type="integer"),
     *              @OA\Property(property="vaccinations[0][name]", type="string"),
     *              @OA\Property(property="vaccinations[0][vaccination_date]", type="date"),
     *              @OA\Property(property="vaccinations[0][duration]", type="integer"),
     *              @OA\Property(property="vaccinations[0][is_expired]", type="integer", enum={"1", "0"}),
     *              @OA\Property(property="deleted_tag_ids[0]",type="integer"),
     *              @OA\Property(property="tags[0][id]", type="integer"),
     *              @OA\Property(property="tags[0][tag_type_id]", type="integer"),
     *              @OA\Property(property="tags[0][factory_number]", type="string"),
     *              @OA\Property(property="tags[0][number]", type="string"),
     *              @OA\Property(property="tags[0][status]", type="integer", enum={"0"," 1"}),
     *              @OA\Property(property="primary_color_id", type="integer"),
     *              @OA\Property(property="secondary_color_id", type="integer"),
     *              @OA\Property(property="tertiary_color_id", type="integer"),
     *              @OA\Property(property="age", type="string", enum={"young", "adult", "senior"}),
     *              @OA\Property(property="birth_date", type="date"),
     *              @OA\Property(property="gender", type="string", enum={"male", "female"}),
     *              @OA\Property(property="weight", type="float"),
     *              @OA\Property(property="size", type="string", enum={"small", "medium", "large"}),
     *              @OA\Property(property="digital_link", type="string"),
     *              @OA\Property(property="generate_public", type="boolean", enum={0, 1}),
     *              @OA\Property(property="ownership_date", type="date"),
     *              @OA\Property(property="cover_image", type="file"),
     *              @OA\Property(property="file_image", type="file"),
     *              @OA\Property(property="_method", type="string", format="string", example="PUT")
     *           )
     *       )
     *   ),
     * @OA\Response(
     *         response="200",
     *         description="successful operation",
     *     ),
     * )
     */
    public function update(UserUpdateAnimalRequest $request, Animal $animal)
    {
        $this->animalService->update($request, $animal, true);

        return response()->json(new AnimalResource($animal), 200);
    }

    /**
     * @OA\Delete(
     * path="/user/animals/{id}",
     * description="Delete entered animal.",
     *     @OA\Parameter(
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(type="string"),
     *         @OA\Examples(example="int", value="1", summary="An int value."),
     *      ),
     * operationId="delete_animal_for_me",
     * tags={"User - Animals"},
     * security={{"bearer_token":{}}},
     * @OA\Response(
     *    response=204,
     *    description="successful operation"
     * ),
     * )
     *)
     */
    public function destroy(Animal $animal)
    {
        $this->animalService->delete($animal);
        
        return response()->json(null, 204);
    }
    /**
     * @OA\Post(
     * path="/user/animals/{id}/generate-token",
     * description="Generate token.",
     * @OA\Parameter(
     *     in="path",
     *     name="id",
     *     required=true,
     *     @OA\Schema(type="string"),
     * ),
     * tags={"User - Animals"},
     * security={{"bearer_token": {}}},
     * @OA\Response(
     *         response="200",
     *         description="successful operation",
     *     ),
     * )
     */

    public function generateTransferToken(Animal $animal)
    {
        $token = $this->animalService->generateTransferToken($animal);

        return response()->json(['token' => $token], 200);
    }

    /**
     * @OA\Post(
     * path="/user/animals/accept-transfer",
     * description="Accept animal transfer.",
     * tags={"User - Animals"},
     * security={{"bearer_token": {}}},
     *   @OA\RequestBody(
     *       required=true,
     *       @OA\MediaType(
     *           mediaType="multipart/form-data",
     *           @OA\Schema(
     *              required={"token"},
     *              @OA\Property(property="token", type="string"),
     *           )
     *       )
     *   ),
     * @OA\Response(
     *         response="200",
     *         description="successful operation",
     *     ),
     * )
     */
    public function acceptTransfer(TransferRequest $request)
    {
        $transfer_information = $this->animalService->acceptTransfer($request);

        return response()->json(['transfer_information' => $transfer_information], 200);
    }

    /**
     * @OA\Get(
     * path="/user/animals/{id}/ownership-records",
     * description="Get all ownership records for animal",
     * operationId="get_all_ownership_records_for_user",
     * tags={"User - Animals"},
     *   security={{"bearer_token": {} }},
     * @OA\Parameter(
     *     in="path",
     *     name="id",
     *     required=true,
     *     @OA\Schema(type="string"),
     * ),
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
     *    name="owner_id",
     *    required=false,
     *    @OA\Schema(type="integer"),
     * ),
     * @OA\Response(
     *     response=200,
     *     description="Success",
     *  )
     *  )
     * */

    public function ownershipRecords(GetRequest $request, Animal $animal)
    {
        $ownership_records = $this->animalService->getOwnershipRecords($request, $animal);

        return OwnershipRecordResource::collection($ownership_records);
    }

    /**
     * @OA\Post(
     * path="/user/animals/{id}/report-lost",
     * description="Report a lost animal.",
     * tags={"User - Animals"},
     * security={{"bearer_token": {}}},
     * @OA\Parameter(
     *     in="path",
     *     name="id",
     *     required=true,
     *     @OA\Schema(type="string"),
     * ),
     *   @OA\RequestBody(
     *       required=true,
     *       @OA\MediaType(
     *           mediaType="multipart/form-data",
     *           @OA\Schema(
     *              required={"seen_at", "address", "mark_as_public"},
     *              @OA\Property(property="seen_at", type="date"),
     *              @OA\Property(property="address", type="string"),
     *              @OA\Property(property="mark_as_public", type="integer", enum={"0", "1"}),
     *           )
     *       )
     *   ),
     * @OA\Response(
     *         response="200",
     *         description="successful operation",
     *     ),
     * )
     */
    public function reportLost(LostReportRequest $request, Animal $animal)
    {
        $this->animalService->reportLost($request, $animal);

        return response()->json(new AnimalResource($animal), 200);
    }

    /**
     * @OA\Post(
     * path="/user/animals/{id}/mark-as-found",
     * description="mark animal status as found.",
     * tags={"User - Animals"},
     * security={{"bearer_token": {}}},
     * @OA\Parameter(
     *     in="path",
     *     name="id",
     *     required=true,
     *     @OA\Schema(type="string"),
     * ),
     * @OA\Response(
     *         response="200",
     *         description="successful operation",
     *     ),
     * )
     */
    public function markAsfound(Animal $animal)
    {
        $this->animalService->markAsFound($animal);

        return response()->json(new AnimalResource($animal), 200);
    }
}
