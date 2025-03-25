<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\GetRequest;
use App\Http\Resources\AnimalResource;
use App\Http\Resources\OwnershipRecordResource;
use App\Models\Animal;
use App\Models\OwnershipRecord;
use App\Services\AnimalService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Mosab\Translation\Models\Translation;
use Illuminate\Support\Str;

class AnimalController extends Controller
{
    protected $animalService;

    public function __construct(AnimalService $animalService)
    {
        $this->middleware('auth:sanctum');
        $this->middleware('permission:animals.read|animals.write|animals.delete')->only('index', 'show');
        $this->middleware('permission:animals.read|animals.write|animals.delete|ownershipRecords.read')->only('ownershipRecords');
        $this->middleware('permission:animals.write')->only('store', 'update');
        $this->middleware('permission:animals.delete')->only('destroy');
        $this->middleware('permission:animals.transfer')->only('generateTransferToken', 'acceptTransfer');

        $this->animalService = $animalService;
    }

    /**
     * @OA\Get(
     * path="/admin/animals",
     * description="Get all animals",
     * operationId="get_all_animals",
     * tags={"Admin - Animals"},
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
     *    name="category_id",
     *    required=false,
     *    @OA\Schema(type="integer"),
     * ),
     * @OA\Parameter(
     *    in="query",
     *    name="animal_type_id",
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
     *    name="owner_id",
     *    required=false,
     *    @OA\Schema(type="integer"),
     * ),
     * @OA\Parameter(
     *    in="query",
     *    name="branch_id",
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
            'category_id'        => ['integer', 'exists:categories,id'],
            'animal_type_id'     => ['integer', 'exists:animal_types,id'],
            'animal_specie_id'   => ['integer', 'exists:animal_species,id'],
            'animal_breed_id'    =>  ['integer', 'exists:animal_breeds,id'],
            'owner_id'            =>  ['integer', 'exists:users,id'],
            'branch_id'            =>  ['integer', 'exists:branches,id'],
            'uaid'                 => ['string', 'exists:animals,uaid'],
            'tag_number'           => ['string', 'exists:tags,number'],
            'q'                  => ['string']
        ]);

        $q = Animal::query()->with(['category', 'animal_type', 'animal_specie', 'animal_breed', 'pet_marks', 'user', 'media', 'primaryColor', 'secondaryColor', 'tertiaryColor', 'user_create', 'tags', 'sensitivities', 'branch'])->latest();

        if ($request->category_id)
            $q->where('category_id', $request->category_id);
        if ($request->animal_type_id)
            $q->where('animal_type_id', $request->animal_type_id);
        if ($request->animal_specie_id)
            $q->where('animal_specie_id', $request->animal_specie_id);
        if ($request->animal_breed_id)
            $q->where('animal_breed_id', $request->animal_breed_id);
        if ($request->owner_id)
            $q->where('user_id', $request->owner_id);
        if ($request->branch_id)
            $q->where('branch_id', $request->branch_id);

        if ($request->uaid)
            $q->where('uaid', $request->uaid);

        if ($request->tag_number) {
            $q->whereHas('tags', function ($query) use ($request) {
                return $query->where('number', $request->tag_number);
            });
        }

        if ($request->q) {
            $animals_ids = Translation::where('translatable_type', Animal::class)
                ->where('attribute', 'name')
                ->where('value', 'LIKE', '%' . $request->q . '%')
                ->groupBy('translatable_id')
                ->pluck('translatable_id');

            $q->where(function ($query) use ($request, $animals_ids) {
                if (is_numeric($request->q))
                    $query->where('id', $request->q);

                $query->orWhereIn('id', $animals_ids);
            });
        }

        if ($request->with_paginate === '0')
            $animals = $q->get();
        else
            $animals = $q->paginate($request->per_page ?? 10);

        return AnimalResource::collection($animals);
    }

    /**
     * @OA\Post(
     * path="/admin/animals",
     * description="Add new animal.",
     * tags={"Admin - Animals"},
     * security={{"bearer_token": {}}},
     * @OA\RequestBody(
     *       required=true,
     *       @OA\MediaType(
     *           mediaType="multipart/form-data",
     *           @OA\Schema(
     *              required={"name[ar]", "description[ar]", "owner_type" , "owner_id"},
     *              @OA\Property(property="owner_type", type="string", enum={"user", "entity"}),
     *              @OA\Property(property="owner_id", type="integer"),
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
     *              @OA\Property(property="animal_type_id", type="integer"),
     *              @OA\Property(property="animal_specie_id", type="integer"),
     *              @OA\Property(property="animal_breed_id", type="integer"),
     *              @OA\Property(property="pet_mark_ids[0]", type="integer"),
     *              @OA\Property(property="primary_color_id", type="integer"),
     *              @OA\Property(property="primary_color", type="string"),
     *              @OA\Property(property="secondary_color_id", type="integer"),
     *              @OA\Property(property="secondary_color", type="string"),
     *              @OA\Property(property="tertiary_color_id", type="integer"),
     *              @OA\Property(property="tertiary_color", type="string"),
     *              @OA\Property(property="age", type="string", enum={"young", "adult", "senior"}),
     *              @OA\Property(property="birth_date", type="date"),
     *              @OA\Property(property="gender", type="string", enum={"male", "female"}),
     *              @OA\Property(property="size", type="string", enum={"small", "medium", "large"})
     *          )
     *       )
     * ),
     * @OA\Response(
     *    response=200,
     *    description="successful operation",
     * ),
     * )
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'           => ['required', 'array', translation_rule()],
            'description'    => ['required', 'array', translation_rule()],
            'like'    => ['array', translation_rule()],
            'deslike'    => ['array', translation_rule()],
            'good_with'    => ['array', translation_rule()],
            'bad_with'    => ['array', translation_rule()],
            'sensitivities' => ['array'],
            'sensitivities.*' => ['string'],
            'photos'         => ['required', 'array'],
            'photos.*'       => ['image'],
            'owner_type'     => ['required', 'in:user,entity'],
            'owner_id'         => ['required', 'integer', 'exists:users,id'],
            'branch_id'         => ['required_if:owner_type,entity', 'integer', 'exists:branches,id'],
            'category_id'         => ['required', 'integer', 'exists:categories,id'],
            'animal_type_id'      => ['required', 'integer', 'exists:animal_types,id'],
            'animal_specie_id'    => ['required', 'integer', 'exists:animal_species,id'],
            'animal_breed_id'     => ['integer', 'exists:animal_breeds,id'],
            'pet_mark_ids'           => ['array'],
            'pet_mark_ids.*'         => ['integer', 'exists:pet_marks,id'],
            'primary_color_id'    => ['required', 'integer', 'exists:colors,id'],
            'secondary_color_id'  => ['required', 'integer', 'exists:colors,id'],
            'tertiary_color_id'   => ['required', 'integer', 'exists:colors,id'],
            'primary_color'    => ['required', 'string'],
            'secondary_color'  => ['required', 'string'],
            'tertiary_color'   => ['required', 'string'],
            'age' => ['in:young,adult,senior'],
            'gender' => ['required', 'in:male,female'],
            'size' => ['required', 'in:small,medium,large'],
            'link' => ['string'],
            'status' => ['required', 'in:1,0'],
            'birth_date' => ['required', 'date']
        ]);

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


        $animal = Animal::create([
            'name'          => $request->name,
            'description'   => $request->description,
            'like'  => $request->like,
            'deslike' => $request->deslike,
            'good_with' => $request->good_with,
            'bad_with'  => $request->bad_with,
            'owner_type'     => $request->owner_type,
            'user_id'         => $request->owner_id,
            'branch_id'           => $request->branch_id ?? null,
            'category_id'         => $request->category_id,
            'animal_type_id'      => $request->animal_type_id,
            'animal_specie_id'    => $request->animal_specie_id,
            'animal_breed_id'     => $request->animal_breed_id,
            'primary_color_id'    => $request->primary_color_id,
            'secondary_color_id'  => $request->secondary_color_id,
            'tertiary_color_id'   => $request->tertiary_color_id,
            'primary_color'    => $request->primary_color,
            'secondary_color'  => $request->secondary_color,
            'tertiary_color'   => $request->tertiary_color,
            'age' => $request->age ?? null,
            'gender' => $request->gender,
            'size' => $request->size,
            'link' => $request->link ?? null,
            'status' => $request->status,
            'birth_date' => $request->birth_date,
            'user_create_id' => auth()->id(),
            'uaid' => Str::random(15),
        ]);

        if ($request->photos) {
            foreach ($request->photos as $photo) {
                $uploadedphoto = upload_file($photo, 'animals', 'animal');
                $mediaData[] = ['link' => $uploadedphoto];
            }

            $animal->media()->createMany($mediaData);
        }

        if ($request->pet_mark_ids) {
            foreach ($request->pet_mark_ids as $pet_mark_id) {
                $animal->animal_pet_marks()->create(['pet_mark_id' => $pet_mark_id]);
            }
        }

        if ($request->sensitivities) {
            foreach ($request->sensitivities as $sensitivity) {
                $animal->sensitivities()->create(['name' => $sensitivity]);
            }
        }

        $this->animalService->createOwnershipRecord($animal);

        return response()->json(new AnimalResource($animal), 200);
    }

    /**
     * @OA\Get(
     * path="/admin/animals/{id}",
     * description="Get animal information.",
     *     @OA\Parameter(
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(type="string"),
     *      ),
     * operationId="show_animal",
     * tags={"Admin - Animals"},
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
        $animal->load(['category', 'animal_type', 'animal_specie', 'animal_breed', 'pet_marks', 'user', 'media', 'primaryColor', 'secondaryColor', 'tertiaryColor', 'user_create', 'tags', 'sensitivities', 'branch']);
        return response()->json(new AnimalResource($animal), 200);
    }

    /**
     * @OA\Post(
     * path="/admin/animals/{id}",
     * description="Edit animal.",
     * @OA\Parameter(
     *     in="path",
     *     name="id",
     *     required=true,
     *     @OA\Schema(type="string"),
     * ),
     * tags={"Admin - Animals"},
     * security={{"bearer_token": {}}},
     * @OA\RequestBody(
     *       required=true,
     *       @OA\MediaType(
     *           mediaType="multipart/form-data",
     *           @OA\Schema(
     *              @OA\Property(property="owner_type", type="string", enum={"user", "entity"}),
     *              @OA\Property(property="owner_id", type="integer"),
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
     *              @OA\Property(property="animal_type_id", type="integer"),
     *              @OA\Property(property="animal_specie_id", type="integer"),
     *              @OA\Property(property="animal_breed_id", type="integer"),
     *              @OA\Property(property="pet_mark_ids[0]", type="integer"),
     *              @OA\Property(property="deleted_pet_mark_ids[0]", type="integer"),
     *              @OA\Property(property="primary_color_id", type="integer"),
     *              @OA\Property(property="primary_color", type="string"),
     *              @OA\Property(property="secondary_color_id", type="integer"),
     *              @OA\Property(property="secondary_color", type="string"),
     *              @OA\Property(property="tertiary_color_id", type="integer"),
     *              @OA\Property(property="tertiary_color", type="string"),
     *              @OA\Property(property="age", type="string", enum={"young", "adult", "senior"}),
     *              @OA\Property(property="birth_date", type="date"),
     *              @OA\Property(property="gender", type="string", enum={"male", "female"}),
     *              @OA\Property(property="size", type="string", enum={"small", "medium", "large"}),
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
    public function update(Request $request, Animal $animal)
    {
        $request->validate([
            'name'           => ['required', 'array', translation_rule()],
            'description'    => ['required', 'array', translation_rule()],
            'like'    => ['array', translation_rule()],
            'deslike'    => ['array', translation_rule()],
            'good_with'    => ['array', translation_rule()],
            'bad_with'    => ['array', translation_rule()],
            'sensitivities' => ['array'],
            'sensitivities.*' => ['string'],
            'deleted_sensitivity_ids' => ['array'],
            'deleted_sensitivity_ids.*' => ['integer', 'exists:animal_sensitivities,id'],
            'owner_type'     => ['required', 'in:user,entity'],
            'owner_id'         => ['required', 'integer', 'exists:users,id'],
            'branch_id'         => ['required_if:owner_type,entity', 'integer', 'exists:branches,id'],
            'category_id'         => ['required', 'integer', 'exists:categories,id'],
            'animal_type_id'      => ['required', 'integer', 'exists:animal_types,id'],
            'animal_specie_id'    => ['required', 'integer', 'exists:animal_species,id'],
            'animal_breed_id'     => ['integer', 'exists:animal_breeds,id'],
            'pet_mark_ids'         => ['array'],
            'deleted_pet_mark_ids'  => ['array'],
            'pet_mark_ids.*'     => ['integer', 'exists:pet_marks,id'],
            'deleted_pet_mark_ids.*'     => ['integer', 'exists:pet_marks,id'],
            'primary_color_id'    => ['required', 'integer', 'exists:colors,id'],
            'secondary_color_id'  => ['required', 'integer', 'exists:colors,id'],
            'tertiary_color_id'   => ['required', 'integer', 'exists:colors,id'],
            'primary_color'    => ['required', 'string'],
            'secondary_color'  => ['required', 'string'],
            'tertiary_color'   => ['required', 'string'],
            'age' => ['in:young,adult,senior'],
            'birth_date' => ['required', 'date'],
            'gender' => ['required', 'in:male,female'],
            'size' => ['required', 'in:small,medium,large'],
            'link' => ['string'],
            'status' => ['required', 'in:1,0'],
            'deleted_media_ids' => ['array'],
            'deleted_media_ids.*' => ['integer', 'exists:media,id'],
            'photos' => ['array'],
            'photos.*' => ['required'],
        ]);

        $old_owner_id = $animal->user_id;

        if ($request->deleted_media_ids) {
            $photos = $animal->media()->whereIn('id', $request->deleted_media_ids)->get();

            foreach ($photos as $photo) {
                delete_file_if_exist($photo->link);
            }

            $animal->media()->whereIn('id', $request->deleted_media_ids)->delete();
        }

        foreach ($request->photos as $photo) {

            $media = $animal->media()->where('link', $photo)->first();

            if ($media) {
                $media->link = $photo;
                $media->save();
            } else {
                if (is_file($photo)) {
                    $uploadedphoto = upload_file($photo, 'animals', 'animal');
                    $animal->media()->create([
                        'link' => $uploadedphoto,
                    ]);
                } else
                    throw ValidationException::withMessages(['image' => __('error_messages.Image should be a file')]);
            }
        }

        $animal->update([
            'name'          => $request->name,
            'description'   => $request->description,
            'like' =>  $request->like,
            'deslike' => $request->deslike,
            'good_with' => $request->good_with,
            'bad_with' => $request->bad_with,
            'owner_type'     => $request->owner_type,
            'user_id'         => $request->owner_id,
            'branch_id'       => $request->branch_id,
            'category_id'         => $request->category_id,
            'animal_type_id'      => $request->animal_type_id,
            'animal_specie_id'    => $request->animal_specie_id,
            'animal_breed_id'     => $request->animal_breed_id ?? null,
            'primary_color_id'    => $request->primary_color_id,
            'secondary_color_id'  => $request->secondary_color_id,
            'tertiary_color_id'   => $request->tertiary_color_id,
            'primary_color'    => $request->primary_color,
            'secondary_color'  => $request->secondary_color,
            'tertiary_color'   => $request->tertiary_color,
            'age' => $request->age ?? null,
            'gender' => $request->gender,
            'size' => $request->size,
            'link' => $request->link ?? null,
            'status' => $request->status,
            'birth_date' => $request->birth_date,
        ]);

        if ($request->owner_id && $request->owner_id != $old_owner_id) {
            $ownership_record = OwnershipRecord::where('animal_id', $animal->id)
                ->where('user_id', $old_owner_id)->first();

            $this->animalService->updateOwnershipRecord($ownership_record);
            $this->animalService->createOwnershipRecord($animal);
        }

        if ($request->deleted_pet_mark_ids) {
            $animal->animal_pet_marks()->whereIn('pet_mark_id', $request->deleted_pet_mark_ids)->delete();
        }

        if ($request->pet_mark_ids) {
            foreach ($request->pet_mark_ids as $pet_mark_id) {
                $is_exists = $animal->animal_pet_marks()->where('pet_mark_id', $pet_mark_id)->exists();

                if (!$is_exists) {
                    $animal->animal_pet_marks()->create(['pet_mark_id' => $pet_mark_id]);
                }
            }
        }

        if ($request->deleted_sensitivity_ids) {
            $animal->sensitivities()->whereIn('id', $request->deleted_sensitivity_ids)->delete();
        }

        if ($request->sensitivities) {
            foreach ($request->sensitivities as $sensitivity) {
                $is_exists = $animal->sensitivities()->where('name', $sensitivity)->exists();

                if (!$is_exists)
                    $animal->sensitivities()->create(['name' => $sensitivity]);
            }
        }

        return response()->json(new AnimalResource($animal), 200);
    }

    /**
     * @OA\Delete(
     * path="/admin/animals/{id}",
     * description="Delete entered animal.",
     *     @OA\Parameter(
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(type="string"),
     *         @OA\Examples(example="int", value="1", summary="An int value."),
     *      ),
     * operationId="delete_animal",
     * tags={"Admin - Animals"},
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
        $photos = $animal->media()->get();

        foreach ($photos as $photo) {
            delete_file_if_exist($photo->link);
        }

        $animal->transfers()->delete();
        $animal->animal_status()->delete();
        $animal->ownership_records()->delete();
        $animal->sensitivities()->delete();
        $animal->tags()->delete();
        $animal->animal_pet_marks()->delete();
        $animal->media()->delete();
        $animal->delete();

        return response()->json(null, 204);
    }

    /**
     * @OA\Post(
     * path="/admin/animals/{id}/generate-token",
     * description="Generate token.",
     * @OA\Parameter(
     *     in="path",
     *     name="id",
     *     required=true,
     *     @OA\Schema(type="string"),
     * ),
     * tags={"Admin - Animals"},
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
     * @OA\Get(
     *   path="/admin/animals/{id}/ownership-records",
     *   description="Get all ownership records for animal",
     *   operationId="get_all_ownership_records",
     *   tags={"Admin - Animals"},
     *   security={{"bearer_token": {}}},
     *   
     *   @OA\Parameter(
     *     in="path",
     *     name="id",
     *     required=true,
     *     @OA\Schema(type="string")
     *   ),
     *   
     *   @OA\Parameter(
     *     in="query",
     *     name="with_paginate",
     *     required=false,
     *     @OA\Schema(type="integer", enum={0, 1})
     *   ),
     *   
     *   @OA\Parameter(
     *     in="query",
     *     name="per_page",
     *     required=false,
     *     @OA\Schema(type="integer")
     *   ),
     *   
     *   @OA\Parameter(
     *     in="query",
     *     name="owner_id",
     *     required=false,
     *     @OA\Schema(type="integer")
     *   ),
     *@OA\Response(
     *     response=200,
     *     description="Success",
     *  )
     *  )
     */

    public function ownershipRecords(GetRequest $request, Animal $animal)
    {
        $ownership_records = $this->animalService->getOwnershipRecords($request, $animal);

        return OwnershipRecordResource::collection($ownership_records);
    }
}
