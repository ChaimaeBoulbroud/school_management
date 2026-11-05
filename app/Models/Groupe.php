<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany; // Added

class Groupe extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'niveau',
        'annee_scolaire',
    ];

   public function eleves()
    {
        return $this->hasMany(Eleve::class, 'groupe_id');
    }

   
    public function affectationCours()
    {
        return $this->hasMany(AffectationCours::class, 'groupe_id');
    }

    /**
     * Get the evaluations for this group.
     */
    public function evaluations()
    {
        return $this->hasMany(Evaluation::class, 'groupe_id');
    }
}
