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
        Schema::create('cash_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cash_session_id')->constrained()->onDelete('cascade');
            $table->foreignId('order_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('payment_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('restrict'); // Caissier
            
            $table->string('transaction_number')->unique(); // Format: TXN-YYYYMMDD-0001
            $table->enum('type', ['sale', 'refund', 'adjustment', 'other'])->default('sale');
            $table->string('payment_method');
            
            $table->decimal('amount', 15, 2);
            $table->text('description')->nullable();
            
            $table->json('metadata')->nullable(); // Infos supplémentaires
            
            $table->timestamp('transaction_at');
            $table->timestamps();
            
            // Index
            $table->index(['cash_session_id', 'type']);
            $table->index('transaction_number');
            $table->index('transaction_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_transactions');
    }
};
