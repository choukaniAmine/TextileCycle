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
    Schema::create('demande_dons', function (Blueprint $table) {
        $table->id();
        $table->foreignId('vetement_id')->constrained()->cascadeOnDelete();
        $table->foreignId('demandeur_id')->constrained('users')->cascadeOnDelete();
        $table->text('message')->nullable();
        $table->string('statut')->default('en_attente');
        $table->timestamps();

        $table->index(['vetement_id', 'statut']);
    });
}

public function down(): void
{
    Schema::dropIfExists('demande_dons');
}
};
