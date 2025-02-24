<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\AnimalResource;
use App\Models\Animal;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Mosab\Translation\Models\Translation;
use Illuminate\Validation\ValidationException;

class AnimalController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->middleware('permission:animals.read|animals.write|animals.delete')->only('index', 'show');
        $this->middleware('permission:animals.write')->only('store', 'update');
        $this->middleware('permission:animals.delete')->only('destroy');
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
     *    name="user_id",
     *    required=false,
     *    @OA\Schema(type="integer"),
     * ),
     * @OA\Parameter(
     *    in="query",
     *    name="entity_id",
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
            'category_id'        => ['integer', 'exists:categories,id'],
            'animal_type_id'     => ['integer', 'exists:animal_types,id'],
            'animal_specie_id'   => ['integer', 'exists:animal_species,id'],
            'animal_breed_id'    =>  ['integer', 'exists:animal_breeds,id'],
            'user_id'            =>  ['integer', 'exists:users,id'],
            'entity_id'          =>  ['integer', 'exists:entities,id'],
            'q'                  => ['string']
        ]);

        $q = Animal::query()->with(['category', 'animal_type', 'animal_specie', 'animal_breed', 'entity', 'branch', 'media', 'primaryColor', 'secondaryColor', 'tertiaryColor'])->latest();

        if ($request->category_id)
            $q->where('category_id', $request->category_id);
        if ($request->animal_type_id)
            $q->where('animal_type_id', $request->animal_type_id);
        if ($request->animal_specie_id)
            $q->where('animal_specie_id', $request->animal_specie_id);
        if ($request->animal_breed_id)
            $q->where('animal_breed_id', $request->animal_breed_id);
        if ($request->user_id)
            $q->where('user_id', $request->user_id);
        if ($request->entity_id)
            $q->where('entity_id', $request->entity_id);


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
     *              required={"name[ar]", "description[ar]", "owner_type" },
     *              @OA\Property(property="owner_type", type="string", enum={"user", "entity"}),
     *              @OA\Property(property="user_id", type="integer"),
     *              @OA\Property(property="entity_id", type="integer"),
     *              @OA\Property(property="branch_id", type="integer"),
     *              @OA\Property(property="name[en]", type="string"),
     *              @OA\Property(property="name[ar]", type="string"),
     *              @OA\Property(property="description[en]", type="string"),
     *              @OA\Property(property="description[ar]", type="string"),
     *              @OA\Property(property="link", type="string"),
     *              @OA\Property(property="status", type="boolean", enum={0, 1}),
     *              @OA\Property(property="photos", type="array", @OA\Items(type="file")),
     *              @OA\Property(property="category_id", type="integer"),
     *              @OA\Property(property="animal_type_id", type="integer"),
     *              @OA\Property(property="animal_specie_id", type="integer"),
     *              @OA\Property(property="animal_breed_id", type="integer"),
     *              @OA\Property(property="primary_color_id", type="integer"),
     *              @OA\Property(property="primary_color", type="string"),
     *              @OA\Property(property="secondary_color_id", type="integer"),
     *              @OA\Property(property="secondary_color", type="string"),
     *              @OA\Property(property="tertiary_color_id", type="integer"),
     *              @OA\Property(property="tertiary_color", type="string"),
     *              @OA\Property(property="age", type="string", enum={"young", "adult", "senior"}),
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
            'photos'         => ['required', 'array'],
            'photos.*'       => ['image'],
            'owner_type'     => ['required', Rule::in(['user', 'entity'])],
            'user_id'         => ['integer', 'exists:users,id'],
            'entity_id'         => ['integer', 'exists:entities,id'],
            'branch_id'         => ['integer', 'exists:branches,id'],
            'category_id'         => ['required', 'integer', 'exists:categories,id'],
            'animal_type_id'      => ['required', 'integer', 'exists:animal_types,id'],
            'animal_specie_id'    => ['required', 'integer', 'exists:animal_species,id'],
            'animal_breed_id'     => ['required', 'integer', 'exists:animal_breeds,id'],
            'primary_color_id'    => ['required', 'integer', 'exists:colors,id'],
            'secondary_color_id'  => ['required', 'integer', 'exists:colors,id'],
            'tertiary_color_id'   => ['required', 'integer', 'exists:colors,id'],
            'primary_color'    => ['required', 'string'],
            'secondary_color'  => ['required', 'string'],
            'tertiary_color'   => ['required', 'string'],
            'age' => ['required',  Rule::in(['young', 'adult', 'senior'])],
            'gender' => ['required', Rule::in(["male", "female"])],
            'size' => ['required',  Rule::in(["small", "medium", "large"])],
            'link' => ['string'],
            'status' => ['required', Rule::in([1, 0])],
        ]);

        $animal = Animal::create([
            'name'          => $request->name,
            'description'   => $request->description,
            'owner_type'     => $request->owner_type,
            'user_id'         => $request->user_id ?? null,
            'entity_id'         => $request->entity_id ?? null,
            'branch_id'         => $request->branch_id ?? null,
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
            'age' => $request->age,
            'gender' => $request->gender,
            'size' => $request->size,
            'link' => $request->link ?? null,
            'status' => $request->status,
        ]);

        if ($request->photos) {
            foreach ($request->photos as $photo) {
                $uploadedphoto = upload_file($photo, 'animals', 'animal');
                $mediaData[] = ['link' => $uploadedphoto];
            }

            $animal->media()->createMany($mediaData);
        }

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
        $animal->load(['category', 'animal_type', 'animal_specie', 'animal_breed', 'entity', 'branch', 'media', 'primaryColor', 'secondaryColor', 'tertiaryColor']);
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
     *              @OA\Property(property="user_id", type="integer"),
     *              @OA\Property(property="entity_id", type="integer"),
     *              @OA\Property(property="branch_id", type="integer"),
     *              @OA\Property(property="name[en]", type="string"),
     *              @OA\Property(property="name[ar]", type="string"),
     *              @OA\Property(property="description[en]", type="string"),
     *              @OA\Property(property="description[ar]", type="string"),
     *              @OA\Property(property="link", type="string"),
     *              @OA\Property(property="status", type="boolean", enum={0, 1}),
     *              @OA\Property(property="photos", type="array", @OA\Items(type="file")),
     *              @OA\Property(property="category_id", type="integer"),
     *              @OA\Property(property="animal_type_id", type="integer"),
     *              @OA\Property(property="animal_specie_id", type="integer"),
     *              @OA\Property(property="animal_breed_id", type="integer"),
     *              @OA\Property(property="primary_color_id", type="integer"),
     *              @OA\Property(property="primary_color", type="string"),
     *              @OA\Property(property="secondary_color_id", type="integer"),
     *              @OA\Property(property="secondary_color", type="string"),
     *              @OA\Property(property="tertiary_color_id", type="integer"),
     *              @OA\Property(property="tertiary_color", type="string"),
     *              @OA\Property(property="age", type="string", enum={"young", "adult", "senior"}),
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
            'photos'         => ['required', 'array'],
            'photos.*'       => ['image'],
            'owner_type'     => ['required', Rule::in(['user', 'entity'])],
            'user_id'         => ['integer', 'exists:users,id'],
            'entity_id'         => ['integer', 'exists:entities,id'],
            'branch_id'         => ['integer', 'exists:branches,id'],
            'category_id'         => ['required', 'integer', 'exists:categories,id'],
            'animal_type_id'      => ['required', 'integer', 'exists:animal_types,id'],
            'animal_specie_id'    => ['required', 'integer', 'exists:animal_species,id'],
            'animal_breed_id'     => ['required', 'integer', 'exists:animal_breeds,id'],
            'primary_color_id'    => ['required', 'integer', 'exists:colors,id'],
            'secondary_color_id'  => ['required', 'integer', 'exists:colors,id'],
            'tertiary_color_id'   => ['required', 'integer', 'exists:colors,id'],
            'primary_color'    => ['required', 'string'],
            'secondary_color'  => ['required', 'string'],
            'tertiary_color'   => ['required', 'string'],
            'age' => ['required',  Rule::in(['young', 'adult', 'senior'])],
            'gender' => ['required', Rule::in(["male", "female"])],
            'size' => ['required',  Rule::in(["small", "medium", "large"])],
            'link' => ['string'],
            'status' => ['required', Rule::in([1, 0])],
        ]);

        $animal_photos = $animal->media()->pluck('link')->toArray();

        if ($request->has('photos') && is_array($request->photos)) {

            $photos = array_diff($request->photos, $animal_photos);

            foreach ($photos as $photo) {
                if (!is_file($photo))
                    throw ValidationException::withMessages(['photo' => __('error_messages.Image should be a file')]);

                $uploadedphoto = upload_file($photo, 'animals', 'animal');
                $mediaData[] = ['link' => $uploadedphoto];
            }

            $animal->media()->createMany($mediaData);
        }
        $animal->update([
            'name'          => $request->name,
            'description'   => $request->description,
            'owner_type'     => $request->owner_type,
            'user_id'         => $request->user_id ?? null,
            'entity_id'         => $request->entity_id ?? null,
            'branch_id'         => $request->branch_id ?? null,
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
            'age' => $request->age,
            'gender' => $request->gender,
            'size' => $request->size,
            'link' => $request->link ?? null,
            'status' => $request->stuts,
        ]);

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
     *    response=200,
     *    description="successful operation"
     * ),
     * )
     *)
     */
    public function destroy(Animal $animal)
    {
        $animal->media()->delete();
        $animal->delete();
        return response()->json(null, 204);
    }
}
