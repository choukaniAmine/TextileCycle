<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dons', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            // Relation 1-N : une association reçoit plusieurs dons.
            $table->foreignId('association_id')->constrained()->cascadeOnDelete();
            // Donateur (facultatif : le compte peut être supprimé sans perdre l'historique).
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->text('description');
            $table->unsignedInteger('quantity')->default(1);
            $table->string('status')->default('en_attente')->index();
            $table->date('donated_at');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dons');
    }
};
