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
        'status',
        'link',
        'category_id',
        'animal_type_id',
        'animal_specie_id',
        'animal_breed_id',
        'primary_color_id',
        'primary_color',
        'secondary_color_id',
        'secondary_color',
        'tertiary_color_id',
        'tertiary_color',
        'age',
        'gender',
        'size',
        'birth_date',
        'user_create_id',
        'branch_id',
    ];

    protected $translatable = [
        'name',
        'description',
        'like',
        'deslike',
        'good_with',
        'bad_with',
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

    public function pet_marks()
    {
        return $this->belongsToMany(PetMark::class, 'animal_pet_marks');
    }

    public function animal_pet_marks()
    {
        return $this->hasMany(AnimalPetMark::class, 'animal_id');
    }

    public function tags()
    {
        return $this->hasMany(Tag::class);
    }

    public function user_create()
    {
        return $this->hasOne(User::class, 'id', 'user_create_id');
    }

    public function ownership_records()
    {
        return $this->hasMany(OwnershipRecord::class);
    }

    public function sensitivities()
    {
        return $this->hasMany(AnimalSensitivity::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
 