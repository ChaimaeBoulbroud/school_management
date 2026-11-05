<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('evaluations', function (Blueprint $table) {
            $table->id(); 
            $table->string('titre_evaluation');
            $table->enum('type_evaluation', [ 'Contrôle Continu','Examen Final', ])->default('Contrôle Continu'); // Une valeur par défaut si pertinent
            $table->date('date_evaluation');
            $table->integer('bareme_total')->default(20); // Barème par défaut, ex: sur 20
            $table->foreignId('groupe_id')->constrained('groupes')->onDelete('cascade');
            $table->foreignId('matiere_id')->constrained('matieres')->onDelete('cascade');
            
            $table->timestamps(); // Colonnes created_at et updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluations');
    }
};
