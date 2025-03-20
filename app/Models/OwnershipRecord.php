<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OwnershipRecord extends Model
{
    use HasFactory;

    protected $fillable= [
        'animal_id',
        'user_id',
        'start_date',
        'end_date',
        'duration', 
    ];

    public function animal()
    {
        return $this->belongsTo(Animal::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
