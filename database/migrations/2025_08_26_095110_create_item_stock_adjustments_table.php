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
        Schema::create('item_stock_adjustments', function (Blueprint $table) {
            $table->id();
            $table->integer('item_id')->index();
            $table->decimal('system_stock');
            $table->decimal('actual_stock');
            $table->decimal('adjustment_stock');
            $table->decimal('final_stock');
            $table->string('adjustment_type',20);
            $table->string('unit_id')->index();
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
        Schema::dropIfExists('item_stock_adjustments');
    }
};
