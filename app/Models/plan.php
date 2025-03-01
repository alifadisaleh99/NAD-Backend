<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Mosab\Translation\Database\TranslatableModel;

class plan extends TranslatableModel
{
    use HasFactory;

    protected $fillable = [
        'price',
        'addition_count',
        'status',
        'image',
    ];

    protected $translatable = [
        'name',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
