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
        Schema::create('cash_session_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cash_session_id')->constrained()->onDelete('cascade');
            $table->string('payment_method'); // cash, mobile_money_orange, mobile_money_wave, qr_code, card, other
            
            $table->enum('type', ['opening', 'closing']); // Ouverture ou fermeture
            
            // Montants
            $table->decimal('amount', 15, 2)->default(0);
            
            // Détails supplémentaires pour certains moyens de paiement
            $table->json('details')->nullable(); // Ex: {"provider": "Orange Money", "reference": "OM123456"}
            
            $table->text('notes')->nullable();
            
            $table->timestamps();
            
            // Index
            $table->index(['cash_session_id', 'payment_method', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_session_details');
    }
};
