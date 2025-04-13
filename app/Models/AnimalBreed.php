<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Mosab\Translation\Database\TranslatableModel;

class AnimalBreed extends TranslatableModel
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_id',
        'animal_specie_id',
        'image',
    ];

    protected $translatable = [
        'name',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    
    public function animal_specie()
    {
        return $this->belongsTo(AnimalSpecie::class);
    }
}
