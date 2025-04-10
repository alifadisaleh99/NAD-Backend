<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Mosab\Translation\Database\TranslatableModel;

class AnimalType extends TranslatableModel
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_id',
        'image',
    ];

    protected $translatable = [
        'name',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
