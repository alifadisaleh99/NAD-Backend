<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use HasFactory;

    protected $fillable = [
        'tag_type_id',
        'animal_id',
        'number',
        'factory_number',
        'status',
    ];



    public function animal()
    {
        return $this->belongsTo(Animal::class);
    }

    public function tag_type()
    {
        return $this->belongsTo(TagType::class);
    }
}
