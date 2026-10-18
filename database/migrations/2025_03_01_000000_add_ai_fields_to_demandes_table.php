<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('demandes', function (Blueprint $table) {
            // Caractéristiques utilisées par l'estimation (toutes facultatives : les anciennes demandes restent valides)
            $table->string('categorie_vetement', 40)->nullable()->after('vetement');
            $table->string('type_intervention', 40)->nullable()->after('categorie_vetement');
            $table->unsignedTinyInteger('niveau_difficulte')->nullable()->default(2)->after('type_intervention');

            // Estimation calculée par le service local + réponse complète pour l'audit
            $table->unsignedInteger('duree_estimee_min')->nullable();
            $table->json('estimation')->nullable();

            // Vérité terrain confirmée par l'atelier (données d'apprentissage)
            $table->unsignedInteger('duree_reelle_min')->nullable();
            $table->string('duree_reelle_source', 20)->nullable(); // 'atelier' ou 'demo'
            $table->timestamp('duree_reelle_confirmee_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('demandes', function (Blueprint $table) {
            $table->dropColumn([
                'categorie_vetement', 'type_intervention', 'niveau_difficulte', 'duree_estimee_min',
                'estimation', 'duree_reelle_min', 'duree_reelle_source', 'duree_reelle_confirmee_at',
            ]);
        });
    }
};
