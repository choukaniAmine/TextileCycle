<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Compte (rôle « association ») chargé de traiter les dons reçus par l'association.
        Schema::table('associations', function (Blueprint $table) {
            $table->foreignId('manager_id')->nullable()->unique()->after('id')
                ->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('associations', function (Blueprint $table) {
            $table->dropConstrainedForeignId('manager_id');
        });
    }
};
