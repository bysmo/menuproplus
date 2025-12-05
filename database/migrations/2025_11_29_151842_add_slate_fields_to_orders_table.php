<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('slate_id')->nullable()->after('id')->constrained()->onDelete('set null');
            $table->boolean('is_on_slate')->default(false)->after('slate_id');
            $table->index(['slate_id', 'is_on_slate']);
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['slate_id']);
            $table->dropColumn(['slate_id', 'is_on_slate']);
        });
    }
};
