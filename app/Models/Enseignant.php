<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enseignant extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'prenom',
        'CIN',
        'adresse',
        'date_recrutement',
        'telephone',
        'user_id',
    ];
    protected $casts = [
        'date_recrutement' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the course assignments for this enseignant.
     */
    public function affectationCours()
    {
        return $this->hasMany(AffectationCours::class, 'enseignant_id');
    }

    /**
     * Get the matieres this enseignant teaches (through affectationCours).
     */
    public function matieres()
    {
        return $this->hasManyThrough(
            Matiere::class,
            AffectationCours::class,
            'enseignant_id', // Foreign key on AffectationCours table...
            'id',            // Foreign key on Matiere table...
            'id',            // Local key on Enseignant table...
            'matiere_id'     // Local key on AffectationCours table...
        )->distinct();
    }


    /**
     * Accessor for full name.
     */
    public function getFullNameAttribute()
    {
        return "{$this->prenom} {$this->nom}";
    }
}
