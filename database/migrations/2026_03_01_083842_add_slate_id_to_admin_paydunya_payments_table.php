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
        Schema::create('admin_paydunya_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('slate_id')->nullable()->constrained()->onDelete('set null');
            
            $table->string('paydunya_token')->unique();
            $table->string('invoice_url')->nullable();
            
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('XOF');
            
            $table->string('payment_status')->default('pending');
            $table->string('transaction_id')->nullable();
            
            $table->string('customer_name')->nullable();
            $table->string('customer_email')->nullable();
            $table->string('customer_phone')->nullable();
            
            $table->string('receipt_url')->nullable();
            
            $table->json('payment_response')->nullable();
            $table->json('payment_error_response')->nullable();
            $table->boolean('status')->default(true);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_paydunya_payments');
    }
};
