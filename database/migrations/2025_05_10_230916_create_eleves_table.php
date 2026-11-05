<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::disableForeignKeyConstraints(); 
        Schema::create('eleves', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('prenom');
            $table->enum('genre',['Homme','Femme']);
            $table->date('date_naissance');
            $table->string('lieu_naissance')->nullable();
            $table->string('adresse')->nullable();
            $table->foreignId('parent_id')->constrained('parents')->onDelete('restrict'); // Or cascade if business rule is strict
            $table->foreignId('groupe_id')->constrained('groupes')->onDelete('restrict'); 
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stagiaires');
    }
};