<?php

namespace App\Services;

use App\Http\Resources\OwnershipRecordResource;
use App\Models\Animal;
use App\Models\OwnershipRecord;
use App\Models\Transfer;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

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

        $user = User::find(auth()->id());

        if (is_null($user->entity_id)) {
            $owner_type = 'user';
        } else
            $owner_type = 'entity';

        $ownership_record = OwnershipRecord::where('animal_id', $transfer->animal->id)
            ->where('user_id', $transfer->animal->user_id)->first();

        $this->updateOwnershipRecord($ownership_record);

        $transfer->animal->update([
            'owner_type' => $owner_type,
            'user_id' => auth()->id(),
        ]);

        $this->createOwnershipRecord($transfer->animal);

        $transfer->delete();
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

    public function getOwnershipRecords($request,Animal $animal)
    {
        $q = $animal->ownership_records();

        if($request->owner_id)
           $q->where('user_id', $request->owner_id);

        if ($request->with_paginate === '0')
            $ownership_records = $q->get();
        else
            $ownership_records = $q->paginate($request->per_page ?? 10);

        return  $ownership_records;
    }
    }
