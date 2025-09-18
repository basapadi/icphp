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
        Schema::create('trx_sale_order_details', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('sale_order_id')->index();
            $table->integer('item_id');
            $table->integer('unit_id');
            $table->integer('qty');
            $table->decimal('unit_price');
            $table->decimal('discount')->nullable()->default(0);
            $table->decimal('sub_total');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trx_sale_order_details');
    }
};
