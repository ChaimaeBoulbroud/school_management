<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PersonnelDirection extends Model
{
    use HasFactory;
     protected $table = 'personnel_directions'; 

    protected $fillable = [
        'nom',
        'prenom',
        'CIN',
        'adresse',
        'telephone',
        'rib',
        'user_id',
    ];


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }


    public function getFullNameAttribute()
    {
        return "{$this->prenom} {$this->nom}";
    }
}
