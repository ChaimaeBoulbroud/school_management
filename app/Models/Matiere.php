<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Matiere extends Model
{
    use HasFactory;
     protected $fillable = [
        /*'nom',*/
        'nom_matiere',
        'code_matiere',
        'masse_horaire',
        'coefficient',
    ];
    public function affectationCours()
    {
        return $this->hasMany(AffectationCours::class, 'matiere_id');
    }


    public function evaluations()
    {
        return $this->hasMany(Evaluation::class, 'matiere_id');
    }
}
