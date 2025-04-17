<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Mosab\Translation\Database\TranslatableModel;


class Animal extends TranslatableModel
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'owner_type',
        'user_id',
        'status',
        'link',
        'category_id',
        'animal_specie_id',
        'animal_breed_id',
        'primary_color_id',
        'secondary_color_id',
        'tertiary_color_id',
        'age',
        'gender',
        'size',
        'birth_date',
        'user_create_id',
        'branch_id',
        'uaid',
        'pet_status',
        'weight',
        'digital_link',
        'generate_public',
        'ownership_date',
        'cover_image',
        'file_image',
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

    public function primary_color()
    {
        return $this->hasOne(Color::class, 'id', 'primary_color_id');
    }

    public function secondary_color()
    {
        return $this->hasOne(Color::class, 'id', 'secondary_color_id');
    }

    public function tertiary_color()
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

    public function animal_status()
    {
        return $this->hasOne(AnimalStatus::class, 'animal_id');
    }

    public function transfers()
    {
        return $this->hasMany(Transfer::class);
    }

    public function lost_reports()
    {
        return $this->hasMany(AnimalLostReport::class, 'animal_id');
    }

    public function latest_lost_report()
    {
        return $this->hasOne(AnimalLostReport::class, 'animal_id')
            ->latestOfMany('created_at');
    }

    public function attachments()
    {
        return $this->hasMany(Attachment::class);
    }

    public function vaccinations()
    {
        return $this->hasMany(Vaccination::class);
    }

    public function calculateAge($birth_date)
    {
        if (!is_null($birth_date)) {
            $diff = Carbon::parse($birth_date)->diff(Carbon::now());
            $years = $diff->y;
            $months = $diff->m;
            $days = $diff->d;
        } else {
            $years = 0;
            $months = 0;
            $days = 0;
        }
        $age = [
            'years' => $years,
            'months' => $months,
            'days' => $days,
        ];

        return $age;
    }
}
