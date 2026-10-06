<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('particulier')->after('password')->index();
            $table->string('phone', 30)->nullable()->after('role');
            $table->string('city')->nullable()->after('phone');
            $table->string('organization')->nullable()->after('city'); // nom de l'atelier / association
            $table->text('bio')->nullable()->after('organization');
            $table->boolean('is_active')->default(true)->after('bio');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'phone', 'city', 'organization', 'bio', 'is_active']);
        });
    }
};
