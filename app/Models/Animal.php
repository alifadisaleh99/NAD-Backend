<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Mosab\Translation\Database\TranslatableModel;

class Animal extends TranslatableModel
{
    use HasFactory;

    protected $fillable = [
        'owner_type',
        'user_id',
        'entity_id',
        'branch_id',
        'status',
        'link',
        'category_id',
        'animal_type_id',
        'animal_specie_id',
        'animal_breed_id',
        'pet_mark_id',
        'primary_color_id',
        'primary_color',
        'secondary_color_id',
        'secondary_color',
        'tertiary_color_id',
        'tertiary_color',
        'age',
        'gender',
        'size',
    ];

    protected $translatable = [
        'name',
        'description',
    ];

    public function media()
    {
        return $this->hasMany(Media::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function animal_type()
    {
        return $this->belongsTo(AnimalType::class);
    }

    public function animal_specie()
    {
        return $this->belongsTo(AnimalSpecie::class);
    }

    public function animal_breed()
    {
        return $this->belongsTo(AnimalBreed::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function entity()
    {
        return $this->belongsTo(Entity::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function primaryColor()
    {
        return $this->hasOne(Color::class, 'id', 'primary_color_id');
    }

    public function secondaryColor()
    {
        return $this->hasOne(Color::class, 'id', 'secondary_color_id');
    }

    public function tertiaryColor()
    {
        return $this->hasOne(Color::class, 'id', 'tertiary_color_id');
    }

    public function pet_mark()
    {
        return $this->belongsTo(PetMark::class);
    }
}
