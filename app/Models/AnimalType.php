<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Mosab\Translation\Database\TranslatableModel;

class AnimalType extends TranslatableModel
{
    use HasFactory;

    protected $fillabe = [
        'category_id',
    ];

    protected $translatable = [
        'name',
    ];

    public function category()
    {
        return $this->belongsTo(AnimalType::class);
    }
}
