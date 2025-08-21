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
        Schema::create('trx_received_items', function (Blueprint $table) {
            $table->id();
            $table->string('kode_transaksi')->unique();
            $table->integer('contact_id')->index()->nullable()->comment('ID Pemasok');
            $table->dateTime('tanggal_terima');
            $table->string('diterima_oleh')->nullable();
            $table->text('catatan')->nullable();
            $table->double('total_harga')->default(0);
            $table->double('potongan_harga')->default(0);
            $table->string('status_pembayaran',20);
            $table->string('metode_pembayaran',20);
            $table->string('syarat_pembayaran',20)->nullable();
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
        Schema::dropIfExists('trx_received_items');
    }
};
