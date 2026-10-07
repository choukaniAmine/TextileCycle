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
    Schema::create('vetements', function (Blueprint $table) {
        $table->id();
        $table->string('nom');
        $table->text('description')->nullable();
        $table->string('taille', 10);
        $table->string('etat');   // neuf, tres_bon, bon, use
        $table->string('type');   // don, reparation, transformation
        $table->string('image')->nullable();
        $table->foreignId('categorie_id')->constrained('categories')->restrictOnDelete();
        $table->foreignId('user_id')->constrained()->cascadeOnDelete();
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('vetements');
}
};
