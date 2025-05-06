<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('cart_id')->nullable()->constrained('carts')->onDelete('cascade');
            $table->decimal('delivery_cost',5,2)->default(0);
            $table->decimal('total_price', 10, 2);
            $table->string('notes')->nullable();
            $table->enum('receiving_method', ['delivery', 'pickup'])->default('delivery');
            $table->foreignId('service_id')->nullable()->constrained('services')->cascadeOnDelete();
            $table->enum('payment_method', ['cash', 'online'])->default('cash');
            $table->string('online_method')->nullable();
            $table->string('shipping_address');
            $table->enum('status', ['pending', 'accepted', 'processing', 'shipped', 'delivered', 'cancelled'])->default('pending');
            $table->timestamps();
            $table->softDeletes();
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
