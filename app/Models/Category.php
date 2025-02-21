<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Mosab\Translation\Database\TranslatableModel;

class Category extends TranslatableModel
{
    use HasFactory;

    protected $translatable = [
        'name',
    ];

    public function animalTypes()
    {
        return $this->hasMany(AnimalType::class);
    }
}
