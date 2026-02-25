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
        Schema::create('cash_discrepancies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cash_session_id')->constrained()->onDelete('cascade');
            $table->string('payment_method');
            
            $table->decimal('expected_amount', 15, 2);
            $table->decimal('actual_amount', 15, 2);
            $table->decimal('difference', 15, 2); // actual - expected
            
            $table->enum('type', ['surplus', 'shortage', 'balanced']); // Excédent, manquant, équilibré
            $table->text('justification')->nullable();
            
            $table->boolean('approved')->default(false);
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            
            $table->timestamps();
            
            // Index
            $table->index(['cash_session_id', 'payment_method']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_discrepancies');
    }
};
