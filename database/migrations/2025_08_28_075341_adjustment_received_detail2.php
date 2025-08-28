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
        Schema::table('trx_received_item_details', function (Blueprint $table) {
            $table->date('keladuarsa')->nullable();
            $table->string('batch',50)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trx_received_item_details', function (Blueprint $table) {
            $table->dropColumn('keladuarsa');
            $table->dropColumn('batch');
        });
    }
};
