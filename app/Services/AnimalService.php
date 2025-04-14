<?php

namespace App\Services;

use App\Http\Resources\OwnershipRecordResource;
use App\Models\Animal;
use App\Models\OwnershipRecord;
use App\Models\Transfer;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Mosab\Translation\Models\Translation;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Illuminate\Support\Facades\Auth;

class AnimalService
{

    public function generateTransferToken(Animal $animal)
    {
        $token = Str::random(15);

        Transfer::create([
            'animal_id' => $animal->id,
            'token' => $token,
            'expires_at' => now()->addHours(48),
        ]);

        return $token;
    }

    public function acceptTransfer($request)
    {
        $transfer = Transfer::where('token', $request->token)->first();

        if ($transfer->expires_at < now()) {
            $transfer->delete();

            throw ValidationException::withMessages([
                'token' => __('error_messages.tokin_expired')
            ]);
        }

        if (auth()->id() == $transfer->animal->user_id) {
            throw new BadRequestHttpException(__('error_messages.owner_of_animal'));
        }

        $current_owner = to_user(Auth::user());
        $previous_owner = $transfer->animal->user;

        $branch_id = null;

        if (is_null($current_owner->entity_id)) {
            $owner_type = 'user';
        } else {
            $owner_type = 'entity';
            $branch_id = $current_owner->branch_id;
        }

        $ownership_record = OwnershipRecord::where('animal_id', $transfer->animal->id)
            ->where('user_id', $transfer->animal->user_id)->where('end_date', null)->first();

        $this->updateOwnershipRecord($ownership_record);

        $transfer->animal->update([
            'owner_type' => $owner_type,
            'branch_id'  => $branch_id,
            'user_id' => auth()->id(),
            'ownership_date' => now()->toDateString(),
        ]);

        $this->createOwnershipRecord($transfer->animal);

        $transfer->delete();

        $transfer_information = [
            'previous_owner' => $previous_owner,
            'current_owner'  => $current_owner,
            'animal_uaid'    => $transfer->animal->uaid,
        ];

        return $transfer_information;
    }

    public function createOwnershipRecord(Animal $animal)
    {
        OwnershipRecord::create([
            'animal_id' => $animal->id,
            'user_id' => $animal->user_id,
            'start_date' => $animal->ownership_date,
        ]);
    }

    public function updateOwnershipRecord(OwnershipRecord $ownership_record)
    {
        $ownership_record->update([
            'end_date' => now()->toDateString(),
            'duration' => now()->diffInDays($ownership_record->start_date),
        ]);
    }

    public function getOwnershipRecords($request, Animal $animal)
    {
        $q = $animal->ownership_records()->latest();

        if ($request->owner_id)
            $q->where('user_id', $request->owner_id);

        if ($request->with_paginate === '0')
            $ownership_records = $q->get();
        else
            $ownership_records = $q->paginate($request->per_page ?? 10);

        return  $ownership_records;
    }

    public function getAllAnimals($request, bool $to_owner)
    {
        if ($to_owner) {
            $q = Animal::whereHas('ownership_records', function ($q) {
                $q->where('user_id', auth()->id());
            });
        } else
            $q = Animal::query();

        $q->with(['category', 'animal_specie', 'animal_breed', 'pet_marks', 'user', 'media', 'primary_color', 'secondary_color', 'tertiary_color', 'user_create', 'tags', 'sensitivities', 'branch', 'latest_lost_report'])->latest();

        if ($request->category_id)
            $q->where('category_id', $request->category_id);
        if ($request->primary_color_id)
            $q->where('primary_color_id', $request->primary_color_id);
        if ($request->secondary_color_id)
            $q->where('secondary_color_id', $request->secondary_color_id);
        if ($request->tertiary_color_id)
            $q->where('tertiary_color_id', $request->tertiary_color_id);
        if ($request->gender)
            $q->where('gender', $request->gender);
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
        if ($request->pet_status)
            $q->where('pet_status', $request->pet_status);

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

        return $animals;
    }

    public function create($request, bool $to_owner)
    {
        if ($to_owner) {
            $user = to_user(Auth::user());
            $owner_id = $user->id;
            $owner_type = $user->entity_id ? 'entity' : 'user';
        } else {
            $owner_id = $request->owner_id;
            $owner_type = $request->owner_type;
        }

        $animal = Animal::create([
            'name'          => $request->name,
            'description'   => $request->description,
            'like'  => $request->like,
            'deslike' => $request->deslike,
            'good_with' => $request->good_with,
            'bad_with'  => $request->bad_with,
            'owner_type'     => $owner_type,
            'user_id'         => $owner_id,
            'branch_id'           => $request->branch_id,
            'category_id'         => $request->category_id,
            'animal_specie_id'    => $request->animal_specie_id,
            'animal_breed_id'     => $request->animal_breed_id,
            'primary_color_id'    => $request->primary_color_id,
            'secondary_color_id'  => $request->secondary_color_id,
            'tertiary_color_id'   => $request->tertiary_color_id,
            'age' => $request->age,
            'gender' => $request->gender,
            'size' => $request->size,
            'weight' => $request->weight,
            'link' => $request->link,
            'status' => $request->status,
            'birth_date' => $request->birth_date,
            'user_create_id' => auth()->id(),
            'uaid' => Str::random(15),
            'digital_link' => $request->digital_link, 
            'generate_public' => $request->generate_public, 
            'ownership_date' => $request->ownership_date,
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

        $this->createOwnershipRecord($animal);

        return $animal;
    }

    public function update($request, Animal $animal, bool $to_owner)
    {
        if ($to_owner) {
            $owner_id = $animal->user_id;
            $owner_type = $animal->owner_type;
        } else {
            $old_owner_id = $animal->user_id;
            $owner_id = $request->owner_id;
            $owner_type = $request->owner_type;
        }

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
            'owner_type'     => $owner_type,
            'user_id'         => $owner_id,
            'branch_id'       => $request->branch_id ?? $animal->branch_id,
            'category_id'         => $request->category_id,
            'animal_specie_id'    => $request->animal_specie_id,
            'animal_breed_id'     => $request->animal_breed_id,
            'primary_color_id'    => $request->primary_color_id,
            'secondary_color_id'  => $request->secondary_color_id,
            'tertiary_color_id'   => $request->tertiary_color_id,
            'age' => $request->age,
            'gender' => $request->gender,
            'size' => $request->size,
            'weight' => $request->weight,
            'link' => $request->link,
            'status' => $request->status,
            'birth_date' => $request->birth_date,
            'digital_link' => $request->digital_link, 
            'generate_public' => $request->generate_public, 
        ]);
    
        if ($request->ownership_date) {
            if ($animal->ownership_records->count() == 1 && $request->ownership_date != $animal->ownership_date) {
                $animal->ownership_date = $request->ownership_date;
                $animal->save();
        
                $ownership_record = $animal->ownership_records()->first();
                $ownership_record->start_date = $request->ownership_date;
                $ownership_record->save();
            }
        } if (!$to_owner) {
            if ($owner_id && $owner_id != $old_owner_id) {
                $ownership_record = OwnershipRecord::where('animal_id', $animal->id)
                    ->where('user_id', $old_owner_id)->where('end_date', null)->first();

                $animal->ownership_date = now()->toDateString();               
                $animal->save();

                $this->updateOwnershipRecord($ownership_record);
                $this->createOwnershipRecord($animal);
            }
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
    }

    public function show(Animal $animal)
    {
        $animal->load(['category', 'animal_specie', 'animal_breed', 'pet_marks', 'user', 'media', 'primary_color', 'secondary_color', 'tertiary_color', 'user_create', 'tags', 'sensitivities', 'branch', 'latest_lost_report']);
    }

    public function delete(Animal $animal)
    {
        $photos = $animal->media()->get();

        foreach ($photos as $photo) {
            delete_file_if_exist($photo->link);
        }
        
        $animal->lost_reports()->delete();
        $animal->transfers()->delete();
        $animal->animal_status()->delete();
        $animal->ownership_records()->delete();
        $animal->sensitivities()->delete();
        $animal->tags()->delete();
        $animal->animal_pet_marks()->delete();
        $animal->media()->delete();
        $animal->delete();
    }
    
    public function reportLost($request, Animal $animal)
    {
       if($animal->pet_status != 'lost') {
        $animal->lost_reports()->create([
            'seen_at' => $request->seen_at,
            'address' => $request->address,
            'mark_as_public' => $request->mark_as_public,
        ]);

        $animal->update([
            'pet_status' => 'lost',
        ]);
      }
    }

    public function markAsFound(Animal $animal)
    {
        $animal->update([
            'pet_status' => null,
        ]);
    }
}
