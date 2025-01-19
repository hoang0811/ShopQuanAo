<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Thực hiện migration.
     */
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('product_id')->unsigned();
            $table->bigInteger('order_id')->unsigned();
            $table->decimal('price', 10, 2);
            $table->integer('quantity');
            $table->longText('options')->nullable();
            $table->boolean('rstatus')->default(false);
            $table->timestamps();
            // Định nghĩa khóa ngoại
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
        });
    }

    /**
     * Quay lại migration trước đó.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
