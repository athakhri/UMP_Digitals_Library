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
        Schema::create('return_items', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('loan_item_id')->constrained('loan_items')->cascadeOnDelete(); // 1 item hanya bisa dikembalikan sekali
            $table->foreignUlid('return_id')->constrained('returns')->cascadeOnDelete(); // 1 item hanya bisa dikembalikan sekali
            $table->foreignUlid('book_id')->constrained('books')->cascadeOnDelete(); // 1 item hanya bisa dikembalikan sekali
            $table->integer('quantity');
            $table->text('condition_note')->nullable(); // catatan kerusakan, hilang, dll
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('return_items');
    }
};
