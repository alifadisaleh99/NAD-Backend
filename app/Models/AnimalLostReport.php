<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AnimalLostReport extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
      'animal_id',
      'seen_at',
      'mark_as_public',
      'address',
    ];

    public function animal()
    {
        return $this->belongsTo(Animal::class);
    }
}
