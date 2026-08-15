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
        if (!Schema::hasTable('qr_short_links')) {
            Schema::create('qr_short_links', function (Blueprint $table) {
                $table->id();
                $table->string('code', 16)->unique()->index();
                $table->string('target_type', 32)->index(); // 'table', 'branch'
                $table->unsignedBigInteger('target_id')->index();
                $table->unsignedBigInteger('restaurant_id')->nullable()->index();
                $table->text('destination_url');
                $table->unsignedBigInteger('scan_count')->default(0);
                $table->timestamp('last_scanned_at')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('qr_short_links');
    }
};
