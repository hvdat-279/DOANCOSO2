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
        // Schema::create('shopping_carts', function (Blueprint $table) {
        //     $table->id();
        //     $table->foreignId('users_id')->nullable()->constrained('users')->onDelete('cascade');
        //     $table->foreignId('products_id')->constrained('products')->onDelete('cascade');
        //     $table->string('size');
        //     $table->integer('quantity');
        //     $table->timestamps();

        //     $table->foreign('users_id')->references('id')->on('users')->onDelete('cascade');
        //     $table->foreign('products_id')->references('id')->on('products')->onDelete('cascade');
        // });

        Schema::create('shopping_carts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users', 'id')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products', 'id')->onDelete('cascade');
            $table->string('size');
            $table->integer('quantity');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shopping_carts');
        Schema::dropIfExists('shopping_cart_items');
    }
};
