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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('atelier_id')->constrained('ateliers')->onDelete('cascade');
            $table->string('nom');
            $table->string('type_service'); // Reparation, Retouche, Transformation, Customisation, Upcycling, Autre
            $table->text('description')->nullable();
            $table->decimal('tarif_estime', 8, 2);
            $table->string('duree_estimee')->nullable(); // Ex: 24h, 2 jours, 3h
            $table->boolean('disponible')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
