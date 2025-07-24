<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Loans;
use App\Models\Books;
use App\Models\Returns;

class LoanItem extends Model
{
    use HasUlids;

    protected $table = 'loan_items';

    protected $fillable = [
        'loan_id',
        'book_id',
        'due_date',
        'quantity'
    ];

    protected $casts = [
        'loan_id'  => 'string',
        'book_id'  => 'string',
        'due_date' => 'date',
        'quantity' => 'integer'
    ];

    // Relasi ke loan (peminjaman utama)
    public function loan(): BelongsTo
    {
        return $this->belongsTo(Loans::class);
    }

    // Relasi ke buku
    public function book(): BelongsTo
    {
        return $this->belongsTo(Books::class , 'book_id');
    }

    public function returns() // nama metode bisa plural tapi bebas
{
    return $this->hasOne(Returns::class);
}
}
