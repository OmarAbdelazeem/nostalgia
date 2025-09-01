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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->string('product_number')->unique();
            $table->string('image_url')->nullable(); // Main product image
            $table->decimal('price', 8, 2);
            $table->decimal('discount', 5, 2)->default(0);
            $table->string('manufacturing_material')->nullable();
            $table->string('manufacturing_country')->nullable();
            $table->integer('stock_quantity')->default(0);
            $table->boolean('is_available')->default(true);
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->foreignId('sub_category_id')->nullable()->constrained('categories')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
