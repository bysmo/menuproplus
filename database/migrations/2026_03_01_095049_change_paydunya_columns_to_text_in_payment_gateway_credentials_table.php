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
        Schema::table('payment_gateway_credentials', function (Blueprint $table) {
            $table->text('paydunya_master_key')->nullable()->change();
            $table->text('paydunya_public_key')->nullable()->change();
            $table->text('paydunya_private_key')->nullable()->change();
            $table->text('paydunya_token')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_gateway_credentials', function (Blueprint $table) {
            $table->string('paydunya_master_key')->nullable()->change();
            $table->string('paydunya_public_key')->nullable()->change();
            $table->string('paydunya_private_key')->nullable()->change();
            $table->string('paydunya_token')->nullable()->change();
        });
    }
};
