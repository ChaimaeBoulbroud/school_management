<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('eleve_id')->constrained('eleves')->onDelete('cascade');
            $table->foreignId('evaluation_id')->constrained('evaluations')->onDelete('cascade');

            $table->decimal('note_obtenue', 5, 2); // Ex: 15.50 (5 chiffres au total, 2 après la virgule)
            $table->text('observations')->nullable(); // Commentaires du formateur

            $table->timestamps(); // created_at, updated_at

            // Assurer qu'un stagiaire n'a qu'une seule note pour une évaluation donnée
            $table->unique(['eleve_id', 'evaluation_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notes');
    }
};
