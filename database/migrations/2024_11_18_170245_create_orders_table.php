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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->string('email', 50);
            $table->string('phone', 15)->default('');;
            $table->text('address')->default('');;
            $table->string('payment_method', 50);
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->string('status')->default('0');
            // $table->string('payment_method')->nullable();
            // $table->decimal('total_amount', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
