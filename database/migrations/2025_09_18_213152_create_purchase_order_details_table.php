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
        Schema::create('trx_purchase_order_details', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('purchase_order_id')->index();
            $table->integer('item_id');
            $table->integer('unit_id');
            $table->integer('jumlah')->default(1);
            $table->decimal('harga');
            $table->date('kedaluarsa')->nullable();
            $table->string('batch',30)->nullable();
            $table->integer('sub_total')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trx_purchase_order_details');
    }
};
