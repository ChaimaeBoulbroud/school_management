<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Eleve extends Model
{
    use HasFactory;
     protected $fillable = [
        'nom',
        'prenom',
        'genre',
        'date_naissance',
        'lieu_naissance',
        'adresse',
        'parent_id',
        'groupe_id',
    ];

    public function guardian() 
    {
        return $this->belongsTo(Guardian::class, 'parent_id');
    }


    public function groupe()
    {
        return $this->belongsTo(Groupe::class, 'groupe_id');
    }


    public function notes()
    {
        return $this->hasMany(Note::class, 'eleve_id');
    }


    public function getFullNameAttribute()
    {
        return "{$this->prenom} {$this->nom}";
    }

    // Alias for parent (since 'parent' is a reserved word in PHP and Laravel)
    public function parent()
    {
        return $this->guardian();
    }
}




