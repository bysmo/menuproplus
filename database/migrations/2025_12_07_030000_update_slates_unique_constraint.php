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
        Schema::table('slates', function (Blueprint $table) {
            // Créer une contrainte unique sans le status
            // Cela permet d'avoir plusieurs ardoises pour le même device/restaurant/branch
            // mais avec des statuts différents (active, paid, canceled, etc.)
            $table->unique(['device_uuid', 'restaurant_id', 'branch_id'], 'slates_device_restaurant_branch_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('slates', function (Blueprint $table) {
            // Supprimer la contrainte
            $table->dropUnique('slates_device_restaurant_branch_unique');
        });
    }
};
