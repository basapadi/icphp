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
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('key');
            $table->string('name');
            $table->boolean('is_admin')->default(0);
        });

        Schema::create('role_menus', function (Blueprint $table) {
            $table->id();
            $table->string('role')->index();
            $table->integer('menu_id')->index();
            $table->boolean('view')->default(false);
            $table->boolean('create')->default(false);
            $table->boolean('delete')->default(false);
            $table->boolean('download')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
        Schema::dropIfExists('role_menus');
    }
};
