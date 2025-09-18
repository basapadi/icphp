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
        Schema::table('trx_received_items', function (Blueprint $table) {
            $table->bigInteger('purchase_order_id')->after('id')->index()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trx_received_items', function (Blueprint $table) {
            $table->dropColumn('purchase_order_id');
        });
    }
};
