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
        Schema::create('cart', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('customer_name', 255);
            $table->string('phone_number', 15)->nullable();
            $table->text('address')->nullable();
            $table->decimal('total', 10, 2);
            $table->enum('payment_status', ['lunas', 'dp', 'bayar nanti'])->default('bayar nanti');
            $table->enum('payment_method', ['cash', 'transfer'])->nullable();
            $table->decimal('dp_amount', 10, 2)->nullable();
            $table->integer('quantity')->default(0);
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart');
    }
};
