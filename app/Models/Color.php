<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Mosab\Translation\Database\TranslatableModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class Color extends TranslatableModel
{
    use HasFactory, SoftDeletes;

    protected $translatable = [
        'name',
    ];
}
