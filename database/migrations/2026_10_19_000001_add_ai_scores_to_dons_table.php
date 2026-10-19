<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dons', function (Blueprint $table) {
            $table->decimal('acceptance_probability', 5, 4)->nullable()->after('status');
            $table->decimal('priority_score', 5, 2)->nullable()->after('acceptance_probability');
            $table->timestamp('scored_at')->nullable()->after('priority_score');
            $table->index('priority_score');
        });
    }

    public function down(): void
    {
        Schema::table('dons', function (Blueprint $table) {
            $table->dropIndex(['priority_score']);
            $table->dropColumn(['acceptance_probability', 'priority_score', 'scored_at']);
        });
    }
};