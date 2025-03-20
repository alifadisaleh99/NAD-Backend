<?php

namespace App\Services;

use App\Models\Animal;
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

        if($transfer->expires_at < now())
        {
            $transfer->delete();

            throw ValidationException::withMessages([
                'token' => __('error_messages.transfer_tokin_expired')
            ]);        
        }
    
        $user = User::find(auth()->id());
         
        if(is_null($user->entity_id))
        {
           $owner_type = 'user';
        }
    
        else
            $owner_type = 'entity';
    
        
        $transfer->animal->update([
            'owner_type' => $owner_type,
            'user_id' => auth()->id(),
        ]);

        $transfer->delete();
    
        }
}
