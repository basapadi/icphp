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
        Schema::create('trx_sale_items', function (Blueprint $table) {
            $table->id();
            $table->string('kode_transaksi')->unique();
            $table->bigInteger('sale_order_id')->index()->nullable();
            $table->integer('contact_id')->index()->nullable()->comment('ID Pelanggan');
            $table->dateTime('tanggal_jual');
            $table->string('dijual_oleh')->nullable();
            $table->text('catatan')->nullable();
            $table->unsignedBigInteger('total_harga')->default(0);
            $table->string('status',20);
            $table->integer('created_by');
            $table->integer('updated_by')->nullable();
            $table->integer('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trx_sale_items');
    }
};
