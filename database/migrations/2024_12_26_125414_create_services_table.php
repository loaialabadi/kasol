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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('baner')->nullable();
            $table->string('logo')->nullable();
            $table->string('menu')->nullable();
            $table->decimal('rating',5,2)->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->enum('featured', ['featured', 'not_featured'])->default('not_featured');
            $table->text('description')->nullable();
            $table->time('start_work_date')->nullable();
            $table->time('end_work_date')->nullable();
            $table->string('price_range')->nullable();
            $table->foreignId('image_id')->nullable()->constrained('images')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
