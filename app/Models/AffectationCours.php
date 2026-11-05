<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AffectationCours extends Model
{
    use HasFactory;
    protected $table = 'affectation_cours';
    protected $fillable = [
        'groupe_id',
        'enseignant_id',
        'matiere_id',
    ];


    public function groupe()
    {
        return $this->belongsTo(Groupe::class, 'groupe_id');
    }


    public function enseignant()
    {
        return $this->belongsTo(Enseignant::class, 'enseignant_id');
    }


    public function matiere()
    {
        return $this->belongsTo(Matiere::class, 'matiere_id');
    }
}
