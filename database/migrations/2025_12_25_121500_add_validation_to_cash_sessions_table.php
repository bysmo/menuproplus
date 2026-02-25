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
        Schema::table('cash_sessions', function (Blueprint $table) {
            $table->foreignId('validated_by')->nullable()->constrained('users')->onDelete('set null')->after('closed_by');
            $table->timestamp('validated_at')->nullable()->after('closed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cash_sessions', function (Blueprint $table) {
            $table->dropForeign(['validated_by']);
            $table->dropColumn(['validated_by', 'validated_at']);
        });
    }
};
