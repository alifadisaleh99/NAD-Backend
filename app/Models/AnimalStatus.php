<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Mosab\Translation\Database\TranslatableModel;

class AnimalStatus extends TranslatableModel
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'animal_id',
        'status',
    ];

    protected $translatable = [
        'description'
    ];

    public function animal() 
    {
       return $this->belongsTo(Animal::class);
    }

    public function user() 
    {
       return $this->belongsTo(User::class);
    }
}
