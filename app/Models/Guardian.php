<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guardian extends Model
{
    use HasFactory;
    protected $table = 'parents';
    protected $fillable = [
        'nom',
        'prenom',
        'CIN',
        'telephone',
        'user_id', // Make sure this is fillable if you update it directly
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function eleves()
    {
        return $this->hasMany(Eleve::class, 'parent_id');
    }


    public function getFullNameAttribute()
    {
        return "{$this->prenom} {$this->nom}";
    }

}
