<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('demandes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();            // demandeur
            $table->foreignId('atelier_id')->nullable()->constrained('users')->nullOnDelete(); // atelier assigné
            $table->string('titre');
            $table->string('type')->default('reparation');
            $table->string('vetement');
            $table->text('description');
            $table->string('photo')->nullable();
            $table->boolean('urgent')->default(false);
            $table->date('date_souhaitee')->nullable();
            $table->string('statut')->default('en_attente')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('demandes');
    }
};
