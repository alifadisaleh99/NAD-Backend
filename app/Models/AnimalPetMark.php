<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnimalPetMark extends Model
{
    use HasFactory;

    protected $fillable = [
        'pet_mark_id',
        'animal_id',
    ];
}
