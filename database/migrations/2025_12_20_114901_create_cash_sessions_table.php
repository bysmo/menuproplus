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
        Schema::create('cash_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->onDelete('cascade');
            $table->foreignId('opened_by')->constrained('users')->onDelete('restrict');
            $table->foreignId('closed_by')->nullable()->constrained('users')->onDelete('restrict');
            
            $table->string('session_number')->unique(); // Format: CS-YYYYMMDD-001
            $table->enum('status', ['open', 'closed'])->default('open');
            
            // Timestamps d'ouverture et fermeture
            $table->timestamp('opened_at');
            $table->timestamp('closed_at')->nullable();
            
            // Montants
            $table->decimal('opening_balance', 15, 2)->default(0); // Fond de caisse total
            $table->decimal('expected_balance', 15, 2)->default(0); // Solde théorique
            $table->decimal('closing_balance', 15, 2)->nullable(); // Solde réel compté
            $table->decimal('discrepancy', 15, 2)->default(0); // Écart (closing - expected)
            
            // Statistiques de la session
            $table->integer('total_transactions')->default(0);
            $table->decimal('total_sales', 15, 2)->default(0);
            $table->decimal('total_cash', 15, 2)->default(0);
            $table->decimal('total_mobile_money', 15, 2)->default(0);
            $table->decimal('total_qr_code', 15, 2)->default(0);
            $table->decimal('total_card', 15, 2)->default(0);
            $table->decimal('total_other', 15, 2)->default(0);
            
            // Notes et justifications
            $table->text('opening_notes')->nullable();
            $table->text('closing_notes')->nullable();
            $table->text('discrepancy_justification')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Index
            $table->index(['branch_id', 'status']);
            $table->index('session_number');
            $table->index('opened_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_sessions');
    }
};
