<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('devis', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->foreignId('atelier_id')->constrained('ateliers')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('nom_client');
            $table->string('email_client');
            $table->string('telephone_client')->nullable();
            $table->text('remarques')->nullable();
            $table->decimal('montant_total', 8, 2)->default(0);
            $table->decimal('co2_economise', 6, 2)->default(0);
            $table->unsignedInteger('eau_economisee')->default(0);
            $table->unsignedInteger('duree_estimee_jours')->default(3);
            $table->string('statut')->default('valide'); // valide, accepte, annule
            $table->timestamps();
        });

        Schema::create('devis_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('devis_id')->constrained('devis')->cascadeOnDelete();
            $table->foreignId('service_id')->nullable()->constrained('services')->nullOnDelete();
            $table->string('nom_service');
            $table->string('type_service')->nullable();
            $table->decimal('prix_unitaire', 8, 2)->default(0);
            $table->unsignedInteger('quantite')->default(1);
            $table->decimal('sous_total', 8, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('devis_items');
        Schema::dropIfExists('devis');
    }
};
