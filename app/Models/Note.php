<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Note extends Model
{
    use HasFactory;

    protected $fillable = [
        'note_obtenue',
        'observations',
        'eleve_id',
        'evaluation_id',
    ];

    protected $casts = [
        'note_obtenue' => 'decimal:2', // Pour que Laravel le traite comme un nombre décimal
    ];

    public function eleve()
    {
        return $this->belongsTo(Eleve::class, 'eleve_id');
    }

    /**
     * Get the evaluation this note is for.
     */
    public function evaluation()
    {
        return $this->belongsTo(Evaluation::class, 'evaluation_id');
    }
}
