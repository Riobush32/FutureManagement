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
        Schema::create('wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained(
                table: 'users',
                indexName: 'user_wallets',
            )->onDelete('cascade');
            $table->string('name');
            $table->double('balance')->default(0.00);
            $table->double('currency')->nullable(); 
            $table->string('status', 20)->default('active'); // active, inactive, suspended
            $table->string('description')->nullable();
            $table->string('type', 20)->default('cash'); // personal, business
            $table->double('admin_fee')->default(0.00); // Admin fee for transactions
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallets');
    }
};
