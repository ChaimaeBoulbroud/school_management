<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles; // <--- ADD THIS TRAIT
use Illuminate\Database\Eloquent\Relations\HasOne;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles; // <--- ADD HasRoles HERE

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // --- Relationships to User Profiles ---
    // A User can be an Enseignant (Teacher)
    public function enseignant(): HasOne
    {
        // Assumes 'enseignants' table has a 'user_id' foreign key
        return $this->hasOne(Enseignant::class, 'user_id');
    }

    // A User can be a Parent/Guardian
    public function parentGuardian(): HasOne
    {

        return $this->hasOne(Guardian::class, 'user_id');
    }

    // A User can be a Personnel (Staff)
    public function personnel(): HasOne
    {
        // Assumes your staff model is 'PersonnelDirection' and
        // the 'personnel_directions' table has a 'user_id' foreign key
        return $this->hasOne(PersonnelDirection::class, 'user_id');
    }


}