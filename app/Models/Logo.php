<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Logo extends Model
{
    use HasFactory;

    protected $fillable = [
        'mobile_light_logo',
        'mobile_dark_logo',
        'light_logo',
        'dark_logo',
      ];
}
