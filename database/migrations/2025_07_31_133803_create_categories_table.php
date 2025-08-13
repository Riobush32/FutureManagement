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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained(
                table: 'users',
                indexName: 'categories_index',
            )->onDelete('cascade');
            $table->foreignId('parent_id')->nullable()->constrained(
                table: 'categories',
                indexName: 'parent_categories_index',
            )->onDelete('cascade');
            $table->foreignId('budget_id')->nullable()->constrained(
                table: 'budgets',
                indexName: 'budget_category_index',
            )->onDelete('cascade');
            $table->string('name');
            $table->string('type');
            $table->integer('level')->default(0);
            $table->string('icon')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
