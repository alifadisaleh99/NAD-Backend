<?php

namespace App\Services;

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
    protected $tagService;

    public function __construct(TagService $tagService)
    {
        $this->tagService = $tagService;
    }

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
            'start_date' => $animal->ownership_date ?? now(),
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

        $q->with(['category', 'animal_specie', 'animal_breed', 'pet_marks', 'user', 'media', 'primary_color', 'secondary_color', 'tertiary_color', 'user_create', 'tags', 'sensitivities', 'branch', 'latest_lost_report', 'attachments', 'vaccinations']);

        if ($request->has('ownership_date')) {
            if ($request->ownership_date == 1)
                $q->orderBy('ownership_date', 'desc');
            else
                $q->orderBy('ownership_date', 'asc');
        } else
            $q->latest();

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

        $cover_image = null;
        $file_image = null;
        if($request->cover_image)
            $cover_image = upload_file($request->cover_image, 'animals', 'animal');
        if($request->file_image)
            $file_image = upload_file($request->file_image, 'animals', 'animal');

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
            'cover_image'  => $cover_image,
            'file_image'   => $file_image,
        ]);

        if($request->tags){
            $this->tagService->update($animal, $request->tags, []);
        }

        if ($request->vaccinations) {
            $this->updateVaccinations($animal, $request->vaccinations, []);
        }

        if ($request->attachments) {
            $this->updateAttachments($animal, $request->attachments, []);
        }

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

        $cover_image = null;
        $file_image = null;
        if($request->cover_image)
            $cover_image = $this->updateImage($animal, $request->cover_image, 'cover_image');
        else{
            $is_exists = $animal->media()->where('link', $animal->cover_image)->exists();
            if (!$is_exists)
                 delete_file_if_exist($animal->cover_image);
        }
        if($request->file_image)
            $file_image = $this->updateImage($animal, $request->file_image, 'file_image');
        else{
            $is_exists = $animal->media()->where('link', $animal->file_image)->exists();
            if (!$is_exists)
                delete_file_if_exist($animal->file_image);
        }
        if($request->deleted_tag_ids){
            $this->tagService->update($animal, [], $request->deleted_tag_ids);
        }
        if($request->tags){
            $this->tagService->update($animal, $request->tags, []);
        }

        if($request->deleted_vaccination_ids)
            $this->updateVaccinations($animal, [], $request->deleted_vaccination_ids);
        if($request->vaccinations)
            $this->updateVaccinations($animal, $request->vaccinations, []);

        if ($request->deleted_attachment_ids)
            $this->updateAttachments($animal, [], $request->deleted_attachment_ids);
        if ($request->attachments)
            $this->updateAttachments($animal, $request->attachments, []);

        if ($request->deleted_media_ids) {
            $photos = $animal->media()->whereIn('id', $request->deleted_media_ids)->get();

            foreach ($photos as $photo) {
                if($animal->cover_image != $photo->link && $animal->file_image != $photo->link)
                     delete_file_if_exist($photo->link);
            }

            $animal->media()->whereIn('id', $request->deleted_media_ids)->delete();
        }

        if($request->photos){
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
            'cover_image' => $cover_image,
            'file_image'   => $file_image,
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
        $animal->load(['category', 'animal_specie', 'animal_breed', 'pet_marks', 'user', 'media', 'primary_color', 'secondary_color', 'tertiary_color', 'user_create', 'tags', 'sensitivities', 'branch', 'latest_lost_report', 'attachments', 'vaccinations']);
    }

    public function delete(Animal $animal)
    {
        $photos = $animal->media()->get();
        $attachments = $animal->attachments()->get();

        foreach ($attachments as $attachment) {
            delete_file_if_exist($attachment->file);
        }
        foreach ($photos as $photo) {
            delete_file_if_exist($photo->link);
        }
        delete_file_if_exist($animal->cover_image);
        delete_file_if_exist($animal->file_image);

        $animal->attachments()->delete();
        $animal->vaccinations()->delete();
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

    public function updateAttachments(Animal $animal,  $attachments, $deleted_attachment_ids)
    {
        if (!empty($deleted_attachment_ids)) {
            $deleted_attachments = $animal->attachments()->whereIn('id', $deleted_attachment_ids)->get();

            foreach ($deleted_attachments as $deleted_attachment) {
                delete_file_if_exist($deleted_attachment->file);
            }

            $animal->attachments()->whereIn('id', $deleted_attachment_ids)->delete();
        }

        if (!empty($attachments)) {
            foreach ($attachments as $attachment) {
                if (isset($attachment['id'])) {
                    $animal_attachment = $animal->attachments()->find($attachment['id']);
                    if ($animal_attachment) {
                        $attachment_file = $animal_attachment->file;
                        if (isset($attachment['file'])) {
                            if ($animal_attachment->file == $attachment['file']) {
                                $attachment_file = $attachment['file'];
                            } else {
                                if (!is_file($attachment['file']))
                                    throw ValidationException::withMessages(['file' => __('error_messages.Should be a file')]);
                                delete_file_if_exist($animal_attachment->file);
                                $attachment_file = upload_file($attachment['file'], 'animalAttachments', 'animalAttachment');
                            }
                        }
                        $animal_attachment->update([
                            'name' => $attachment['name'] ?? null,
                            'source' => $attachment['source'] ?? null,
                            'attachment_date' => $attachment['attachment_date'] ?? null,
                            'file' => $attachment_file,
                        ]);
                    }
                } else {
                    $uploadedfile = null;
                    if (isset($attachment['file']))
                        $uploadedfile = upload_file($attachment['file'], 'animalAttachments', 'animalAttachment');

                    $animal->attachments()->create([
                        'name' => $attachment['name'] ?? null,
                        'source' => $attachment['source'] ?? null,
                        'attachment_date' => $attachment['attachment_date'] ?? null,
                        'file' => $uploadedfile,
                    ]);
                }
            }
        }
    }

    public function updateVaccinations(Animal $animal,  $vaccinations, $deleted_vaccination_ids)
    {
        if (!empty($deleted_vaccination_ids))
            $animal->vaccinations()->whereIn('id', $deleted_vaccination_ids)->delete();

        if (!empty($vaccinations)) {
            foreach ($vaccinations as $vaccination) {
                if (isset($vaccination['id'])) {
                    $animal_vaccination = $animal->vaccinations()->find($vaccination['id']);
                    if ($animal_vaccination) {
                        $animal_vaccination->update([
                            'name' => $vaccination['name'] ?? null,
                            'vaccination_date' => $vaccination['vaccination_date'] ?? null,
                            'duration' => $vaccination['duration'] ?? null,
                            'is_expired' => isset($vaccination['is_expired']) ?? null,
                        ]);
                    }
                } else {
                    $animal->vaccinations()->create([
                        'name' => $vaccination['name'] ?? null,
                        'vaccination_date' => $vaccination['vaccination_date'] ?? null,
                        'duration' => $vaccination['duration'] ?? null,
                        'is_expired' => isset($vaccination['is_expired']) ?? null,
                    ]);
                }
            }
        }
    }

    public function updateImage(Animal $animal, $request_image, $type)
    {
        $photo = $animal->media()->where('link', $request_image)->first();
        if ($request_image == $animal->$type) {
            $image = $animal->$type;
        } else if ($photo) {
            $is_exists = $animal->media()->where('link', $animal->$type)->exists();
            if (!$is_exists)
                delete_file_if_exist($animal->$type);

            $image = $photo->link;
        } else {
            if (!is_file($request_image)) {
                throw ValidationException::withMessages([
                    $type => __('error_messages.Image should be a file')
                ]);
            }
            $is_exists = $animal->media()->where('link', $animal->$type)->exists();
            if (!$is_exists)
                delete_file_if_exist($animal->$type);
            $image = upload_file($request_image, 'animals', 'animal');
        }

        return $image;
    }
}
