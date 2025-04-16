<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Attachment extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $fillable = [
        'animal_id',
        'name',
        'source',
        'attachment_date',
        'file',
    ];

    public function animal()
    {
        return $this->belongsTo(Animal::class);
    }
}
