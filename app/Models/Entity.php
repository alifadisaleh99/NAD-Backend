<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Mosab\Translation\Database\TranslatableModel;

class Entity extends TranslatableModel
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'address',
        'email',
        'contact_number',
        'founding_date',
        'price_per_pet',
        'allowed_branches',
        'allowed_users',
        'used_users',
    ];

    protected $translatable = [
        'name',
    ];

}
