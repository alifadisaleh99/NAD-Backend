<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Mosab\Translation\Database\TranslatableModel;

class BranchType extends TranslatableModel
{
    use HasFactory;

    protected $translatable = [
        'name',
    ];
}
