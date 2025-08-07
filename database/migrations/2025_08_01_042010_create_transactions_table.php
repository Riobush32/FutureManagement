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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained(
                table: 'users',
                indexName: 'user_transactions',
            )->onDelete('cascade');
            
            $table->foreignId('budget_id')->nullable()->constrained(
                table: 'budgets',   
                indexName: 'budget_transactions',
            )->nullOnDelete();
            $table->foreignId('category_id')->nullable()->constrained(
                table: 'categories',
                indexName: 'category_transactions',
            )->nullOnDelete();
            $table->text('description')->nullable();
            $table->double('amount')->default(0.00);
            $table->enum('type', ['income', 'expense'])->default('expense'); // income or expense
            $table->date('transaction_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
