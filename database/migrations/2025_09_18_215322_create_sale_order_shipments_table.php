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
        Schema::create('trx_sale_order_shipments', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('sale_order_id')->index();
            $table->date('tanggal_pengiriman')->nullable();
            $table->string('nomor_resi')->nullable();
            $table->string('status',20)->default('pending');
            $table->string('dikirim_oleh')->nullable();
            $table->string('catatan');
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
        Schema::dropIfExists('trx_sale_order_shipments');
    }
};
