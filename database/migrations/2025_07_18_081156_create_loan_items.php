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
        Schema::create('loan_items', function (Blueprint $table) {
            $table->ulid('id')->primary(); // ID unik untuk setiap item peminjaman
            $table->foreignUlid('loan_id')->constrained('loans')->cascadeOnDelete(); // Relasi ke loans
            $table->foreignUlid('book_id')->constrained('books')->cascadeOnDelete(); // Relasi ke buku
            $table->integer('quantity');
            $table->date('due_date'); // Tanggal jatuh tempo
            $table->timestamps(); // created_at, updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_items');
    }
};
