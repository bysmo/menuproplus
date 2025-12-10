<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('slates', function (Blueprint $table) {
            $table->id();

            // Code incrémental unique par branche
            $table->string('code', 20)->unique();

            // UUID de l'appareil (cookie)
            $table->string('device_uuid', 255)->index();

            // Relations
            $table->foreignId('restaurant_id')->constrained()->onDelete('cascade');
            $table->foreignId('branch_id')->constrained()->onDelete('cascade');
            $table->foreignId('customer_id')->nullable()->constrained()->onDelete('set null');

            // Montants
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->decimal('remaining_amount', 10, 2)->default(0);

            // Statut : active, paid, expired
            $table->enum('status', ['active', 'paid', 'expired'])->default('active');

            // Expiration (3 mois)
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('last_activity_at')->nullable();

            $table->timestamps();

            // Index composé pour recherche rapide
            $table->index(['device_uuid', 'branch_id', 'status']);
            $table->index(['code', 'branch_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('slates');
    }
};
