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
        Schema::create('trx_sale_orders', function (Blueprint $table) {
            $table->id();
            $table->string('kode',50)->unique();
            $table->integer('contact_id')->comment('Pelanggan');
            $table->date('tanggal');
            $table->date('tanggal_permintaan')->nullable();
            $table->string('status',20)->default('draft');
            $table->unsignedBigInteger('total')->default(0);
            $table->string('status_pembayaran')->nullable();
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
        Schema::dropIfExists('trx_sale_orders');
    }
};
