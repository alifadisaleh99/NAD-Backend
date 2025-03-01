<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Mosab\Translation\Database\TranslatableModel;

class Category extends TranslatableModel
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'image'
    ];

    protected $translatable = [
        'name',
        'description'
    ];

    public function animalTypes()
    {
        return $this->hasMany(AnimalType::class);
    }

    public function animals()
    {
        return $this->hasMany(Animal::class);
    }
}
