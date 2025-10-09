<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /**
         * TABEL: trx_purchase_invoices
         * -------------------------------------
         * Menyimpan data faktur pembelian dari supplier
         */
        Schema::create('trx_purchase_invoices', function (Blueprint $table) {
            $table->id();
            
            // Relasi utama
            $table->integer('contact_id')->comment('Pemasok');
            $table->integer('purchase_order_id')->nullable();
            
            // Identitas dokumen
            $table->string('kode', 50)->unique();
            $table->date('tanggal');
            $table->string('no_referensi', 50)->nullable();
            
            // Pembayaran & tempo
            $table->string('tipe_bayar', 20)->default('credit'); // cash | credit
            $table->string('syarat_bayar', 50)->nullable(); // contoh: net 30, cod
            $table->date('jatuh_tempo')->nullable();
            $table->string('status_pembayaran', 20)->default('unpaid'); // unpaid | partial | paid
            
            // Nilai keuangan
            $table->unsignedBigInteger('subtotal')->default(0);
            $table->unsignedBigInteger('total_diskon')->default(0);
            $table->unsignedBigInteger('total_pajak')->default(0);
            $table->unsignedBigInteger('biaya_pengiriman')->default(0);
            $table->unsignedBigInteger('grand_total')->default(0);
            $table->unsignedBigInteger('nominal_terbayar')->default(0);
            
            // Status dan catatan
            $table->string('status', 20)->default('draft'); // draft | posted | cancelled
            $table->text('catatan')->nullable();
            
            // Audit trail
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
        });

        /**
         * TABEL: trx_purchase_invoice_details
         * -------------------------------------
         * Menyimpan item-item di dalam faktur pembelian
         */
        Schema::create('trx_purchase_invoice_details', function (Blueprint $table) {
            $table->id();
            $table->integer('purchase_invoice_id');
            $table->integer('item_id');
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
         * TABEL: trx_purchase_payments
         * -------------------------------------
         * Menyimpan transaksi pembayaran untuk faktur pembelian
         */
        Schema::create('trx_purchase_payments', function (Blueprint $table) {
            $table->id();
            $table->integer('purchase_invoice_id');
            $table->date('tanggal');
            $table->string('metode_bayar', 20)->default('cash'); // cash | transfer | giro | ewallet | qris
            $table->string('no_referensi', 50)->nullable();
            $table->unsignedBigInteger('jumlah');
            $table->unsignedBigInteger('diskon')->default(0);
            $table->text('catatan')->nullable();
            
            $table->timestamps();
        });

        /**
         * TABEL: purchase_invoice_item_received (PIVOT)
         * -------------------------------------
         * Relasi many-to-many antara faktur dan penerimaan barang
         */
        Schema::create('trx_purchase_invoice_item_receiveds', function (Blueprint $table) {
            $table->id();
            $table->integer('purchase_invoice_id');
            $table->integer('item_received_id');
            $table->unsignedBigInteger('total_terfaktur')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trx_purchase_payments');
        Schema::dropIfExists('trx_purchase_invoice_details');
        Schema::dropIfExists('trx_purchase_invoices');
    }
};
