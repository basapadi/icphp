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
        Schema::create('trx_sale_shipments', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('item_delivery_id')->index();
            $table->string('tipe_pengiriman',20);
            $table->date('tanggal_kirim')->nullable();
            $table->unsignedBigInteger('biaya_pengiriman')->nullable();
            $table->string('jasa_kirim')->nullable();
            $table->string('no_resi')->nullable();
            $table->string('driver')->nullable();
            $table->string('telepon')->nullable();
            $table->string('no_kendaraan')->nullable();
            $table->string('catatan');
            $table->integer('created_by');
            $table->integer('updated_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trx_sale_shipments');
    }
};
