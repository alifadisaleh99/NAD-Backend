<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Mosab\Translation\Database\TranslatableModel;

class AnimalSpecie extends TranslatableModel
{
    use HasFactory, SoftDeletes;

    protected $fillabe = [
        'category_id',
        'animal_type_id',
    ];

    protected $translatable = [
        'name',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    
    public function animal_type()
    {
        return $this->belongsTo(AnimalType::class);
    }
}
