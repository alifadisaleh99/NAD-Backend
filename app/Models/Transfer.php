<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transfer extends Model
{
    use HasFactory;

    protected $fillable = [
        'animal_id',
        'token',
        'expires_at',
    ];

    public  function animal()
    {
        return $this->belongsTo(Animal::class);
    }
}
