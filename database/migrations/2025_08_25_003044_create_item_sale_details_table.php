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
        Schema::create('trx_sale_item_details', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('item_sale_id')->index();
            $table->integer('item_id');
            $table->decimal('jumlah')->default(0);
            $table->double('harga')->default(0);
            $table->integer('unit_id');
            $table->date('kedaluarsa')->nullable();
            $table->string('batch',50)->nullable();
            $table->integer('sub_total')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trx_sale_item_details');
    }
};
