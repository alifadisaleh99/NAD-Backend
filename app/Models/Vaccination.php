<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vaccination extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'animal_id',
        'name',
        'vaccination_date',
        'duration',
        'is_expired',
    ];

    public function animal()
    {
        return $this->belongsTo(Animal::class);
    }
}
