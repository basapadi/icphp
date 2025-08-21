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
        Schema::create('trx_payment_received_debts', function (Blueprint $table) {
            $table->id();
            $table->integer('contact_id')->index()->comment('ID Pemasok');
            $table->double('jumlah')->default(0);
            $table->string('metode_pembayaran',20);
            $table->string('dibayar_oleh')->nullable();
            $table->dateTime('tanggal_pembayaran');
            $table->string('bukti_bayar')->nullable();
            $table->text('catatan')->nullable();
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
        Schema::dropIfExists('trx_payment_received_debts');
    }
};
