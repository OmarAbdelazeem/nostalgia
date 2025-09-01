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
        Schema::table('products', function (Blueprint $table) {
            // Remove sub_category_id field
            $table->dropForeign(['sub_category_id']);
            $table->dropColumn('sub_category_id');
        });

        Schema::table('categories', function (Blueprint $table) {
            // Remove parent_id field
            $table->dropForeign(['parent_id']);
            $table->dropColumn('parent_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            // Restore parent_id field
            $table->foreignId('parent_id')->nullable()->constrained('categories')->onDelete('cascade');
        });

        Schema::table('products', function (Blueprint $table) {
            // Restore sub_category_id field
            $table->foreignId('sub_category_id')->nullable()->constrained('categories')->onDelete('cascade');
        });
    }
};
