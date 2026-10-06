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
        Schema::create('ateliers', function (Blueprint $table) {
            $table->id();
            $table->string('nom')->unique();
            $table->text('description')->nullable();
            $table->string('adresse');
            $table->string('ville');
            $table->string('code_postal')->nullable();
            $table->string('telephone');
            $table->string('email')->nullable();
            $table->string('image')->nullable();
            $table->string('horaires')->nullable();
            $table->boolean('est_actif')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ateliers');
    }
};
