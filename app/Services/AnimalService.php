<?php

namespace App\Services;

use App\Http\Resources\OwnershipRecordResource;
use App\Models\Animal;
use App\Models\OwnershipRecord;
use App\Models\Transfer;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

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
                'token' => __('error_messages.transfer_tokin_expired')
            ]);
        }
        
        if(auth()->id() == $transfer->animal->user_id)
        {
            throw new BadRequestHttpException(__('error_messages.owner_of_animal'));
        }

        $current_owner = User::find(auth()->id());
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
            'start_date' => now(),
        ]);
    }

    public function updateOwnershipRecord(OwnershipRecord $ownership_record)
    {
        $ownership_record->update([
            'end_date'  => now(),
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

    public function getAnimalByUaidAndTagNumber($request)
    {
        $q = Animal::query()->with(['category', 'animal_type', 'animal_specie', 'animal_breed', 'pet_marks', 'user', 'media', 'primary_color', 'secondary_color', 'tertiary_color', 'user_create', 'tags', 'sensitivities', 'branch'])->latest();

        if ($request->uaid)
            $q->where('uaid', $request->uaid);

        if ($request->tag_number) {
            $q->whereHas('tags', function ($query) use ($request) {
                return $query->where('number', $request->tag_number);
            });
        }

        $animal = $q->first();

     return $animal;
    }
    public function reportLost($request, Animal $animal)
    {
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
