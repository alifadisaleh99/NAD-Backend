<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Mosab\Translation\Database\TranslatableModel;

class TagType extends TranslatableModel
{
    use HasFactory;

    protected $fillable = [
        'icon',
    ];

    protected $translatable = [
        'name'
    ];
}
