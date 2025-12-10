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
            // Ajouter un index unique sur la combinaison device_uuid + restaurant_id + branch_id + status
            // pour empêcher les doublons d'ardoises actives pour le même device/restaurant/branch
            $table->unique(
                ['device_uuid', 'restaurant_id', 'branch_id', 'status'],
                'slates_device_restaurant_branch_status_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('slates', function (Blueprint $table) {
            $table->dropUnique('slates_device_restaurant_branch_status_unique');
        });
    }
};
