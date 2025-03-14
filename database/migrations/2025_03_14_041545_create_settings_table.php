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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->text('phone')->nullable();
            $table->text('email')->nullable();
            $table->text('location')->nullable();
            $table->text('working_hours')->nullable();
            $table->text('min_order_price')->nullable();
            $table->integer('is_stock_minus')->nullable();
            $table->time('operation_hour')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
