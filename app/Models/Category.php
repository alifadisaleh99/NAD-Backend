<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Mosab\Translation\Database\TranslatableModel;

class Category extends TranslatableModel
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'image'
    ];

    protected $translatable = [
        'name',
        'description'
    ];

    public function animalTypes()
    {
        return $this->hasMany(AnimalType::class);
    }

    public function animals()
    {
        return $this->hasMany(Animal::class);
    }

    public function count_animals()
    {
        if(request()->routeIs(['user.categories.index', 'user.categories.show']))
        {
            return $this->animals()->where('user_id', auth()->id())->count();
        }

        else
             return $this->animals->count();
    }
}
