<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /**
         * TABEL: trx_sale_invoices
         * -------------------------------------
         * Menyimpan data faktur penjualan ke pelanggan
         */
        Schema::create('trx_sale_invoices', function (Blueprint $table) {
            $table->id();
            
            // Relasi utama
            $table->integer('contact_id')->index()->comment('Pelanggan');
            
            // Identitas dokumen
            $table->string('kode', 50)->index()->unique();
            $table->date('tanggal');
            $table->string('no_referensi', 50)->nullable();
            
            // Pembayaran & tempo
            $table->string('tipe_bayar', 20)->default('credit'); // cash | credit
            $table->string('syarat_bayar', 50)->nullable(); // contoh: net 30, cod
            $table->date('jatuh_tempo')->nullable();
            $table->string('status_pembayaran', 20)->index()->default('unpaid'); // unpaid | partial | paid
            
            // Nilai keuangan
            $table->unsignedBigInteger('subtotal')->default(0);
            $table->unsignedBigInteger('total_diskon')->default(0);
            $table->unsignedBigInteger('total_pajak')->default(0);
            $table->unsignedBigInteger('biaya_pengiriman')->default(0);
            $table->unsignedBigInteger('grand_total')->default(0);
            $table->unsignedBigInteger('nominal_terbayar')->default(0);
            
            // Status dan catatan
            $table->string('status', 20)->index()->default('draft'); // draft | posted | cancelled
            $table->text('catatan')->nullable();
            
            // Audit trail
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
        });

        /**
         * TABEL: trx_sale_invoice_details
         * -------------------------------------
         * Menyimpan item-item di dalam faktur penjualan
         */
        Schema::create('trx_sale_invoice_details', function (Blueprint $table) {
            $table->id();
            $table->integer('sale_invoice_id')->index();
            $table->integer('item_sale_id')->index();
            $table->integer('item_id')->index();
            $table->integer('unit_id');
            $table->text('catatan')->nullable();
            $table->decimal('jumlah', 12, 2)->default(0);
            $table->unsignedBigInteger('harga')->default(0);
            $table->unsignedBigInteger('diskon_persen')->default(0);
            $table->unsignedBigInteger('diskon_nominal')->default(0);
            $table->unsignedBigInteger('pajak_persen')->default(0);
            $table->unsignedBigInteger('pajak_nominal')->default(0);
            $table->unsignedBigInteger('total')->default(0);
            
            $table->timestamps();
        });

        /**
         * TABEL: trx_sale_payments
         * -------------------------------------
         * Menyimpan transaksi pembayaran untuk faktur penjualan
         */
        Schema::create('trx_sale_payments', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 50)->index()->unique();
            $table->integer('sale_invoice_id')->index();
            $table->date('tanggal');
            $table->string('metode_bayar', 20)->default('cash'); // cash | transfer | giro | ewallet | qris
            $table->string('no_referensi', 50)->nullable();
            $table->unsignedBigInteger('jumlah');
            $table->unsignedBigInteger('diskon')->default(0);
            $table->text('catatan')->nullable();

            // Audit trail
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            
            $table->timestamps();
        });

        /**
         * TABEL: sale_invoice_item_sale (PIVOT)
         * -------------------------------------
         * Relasi many-to-many antara faktur dan penjualan barang
         */
        Schema::create('trx_sale_invoice_item_sales', function (Blueprint $table) {
            $table->id();
            $table->integer('sale_invoice_id')->index();
            $table->integer('item_sale_id')->index();
            $table->unsignedBigInteger('total_terfaktur')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trx_sale_payments');
        Schema::dropIfExists('trx_sale_invoice_details');
        Schema::dropIfExists('trx_sale_invoices');
        Schema::dropIfExists('trx_sale_invoice_item_sales');
    }
};
