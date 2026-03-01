<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payment_gateway_credentials', function (Blueprint $table) {
            $table->string('paydunya_master_key')->nullable()->after('live_xendit_webhook_token');
            $table->string('paydunya_private_key')->nullable()->after('paydunya_master_key');
            $table->string('paydunya_public_key')->nullable()->after('paydunya_private_key');
            $table->string('paydunya_token')->nullable()->after('paydunya_public_key');
            $table->text('paydunya_mode')->nullable()->after('paydunya_token');
            $table->boolean('paydunya_status')->default(false)->after('paydunya_mode');
        });
    }

    public function down(): void
    {
        Schema::table('payment_gateway_credentials', function (Blueprint $table) {
            $table->dropColumn([
                'paydunya_master_key',
                'paydunya_private_key',
                'paydunya_public_key',
                'paydunya_token',
                'paydunya_mode',
                'paydunya_status',
            ]);
        });
    }
};
