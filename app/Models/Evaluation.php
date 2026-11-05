<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Evaluation extends Model
{
    use HasFactory;

    protected $fillable = [
        'titre_evaluation',
        'type_evaluation',
        'date_evaluation',
        'bareme_total',
        'groupe_id',
        'matiere_id',
    ];

    // Pour que Laravel traite 'date_evaluation' comme un objet Carbon
    protected $casts = [
        'date_evaluation' => 'date',
    ];

    public function groupe()
    {
        return $this->belongsTo(Groupe::class, 'groupe_id');
    }

    /**
     * Get the matiere this evaluation is for.
     */
    public function matiere()
    {
        return $this->belongsTo(Matiere::class, 'matiere_id');
    }

    /**
     * Get the notes for this evaluation.
     */
    public function notes()
    {
        return $this->hasMany(Note::class, 'evaluation_id');
    }
}
