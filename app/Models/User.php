<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone_country_id',
        'phone',
        'country_id',
        'entity_id',
        'branch_id',
        'summary',
        'image',
        'password',
        'status',
        'is_owner',
        'national_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function roleModel()
    {
        return $this->roles()->first();
    }

    public function nationality()
    {
        return $this->hasOne(Country::class, 'id', 'country_id');
    }

    public function phone_country()
    {
        return $this->hasOne(Country::class, 'id', 'phone_country_id');
    }

    public function entity()
    {
        return $this->belongsTo(Entity::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function animals()
    {
        return $this->hasMany(Animal::class);
    }

    public function ownership_records()
    {
        $this->hasMany(OwnershipRecord::class);
    }

    public function saveCode($email, $code)
    {
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $email],
            ['email' => $email, 'token' => $code, 'created_at' => now()]
        );
    }
}
