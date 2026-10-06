<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('interventions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('demande_id')->constrained()->cascadeOnDelete();
            $table->foreignId('atelier_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('titre');
            $table->text('description')->nullable();
            $table->string('statut')->default('en_attente');
            $table->decimal('cout_estime', 8, 2)->nullable();
            $table->date('date_debut')->nullable();
            $table->date('date_fin')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('interventions');
    }
};
