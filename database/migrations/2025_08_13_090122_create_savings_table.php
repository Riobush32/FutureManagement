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
        Schema::create('savings', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('user_id')->constrained(
                table: 'users',
                indexName: 'user_savings',
            )->onDelete('cascade');
            $table->foreignId('wallet_id')->constrained(
                table: 'wallets',
                indexName: 'wallet_savings',
            )->onDelete('cascade');
            $table->double('amount')->default(0);
            $table->date('saving_date')->nullable();
            $table->enum('type', ['gold', 'money'])->default('money');
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('savings');
    }
};
