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
        Schema::table('global_settings', function (Blueprint $table) {
            $table->string('privacy_policy_link')->nullable()->after('email');
            $table->string('terms_and_conditions_link')->nullable()->after('privacy_policy_link');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('global_settings', function (Blueprint $table) {
            $table->dropColumn('privacy_policy_link');
            $table->dropColumn('terms_and_conditions_link');
        });
    }
};
