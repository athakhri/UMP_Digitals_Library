<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\LoanItem;
use App\Models\Returns;
use App\Models\Books;


class ReturnItem extends Model
{
    use HasUlids;

    protected $table = 'return_items';

    protected $fillable = [
        'loan_item_id',
        'return_id',
        'book_id',
        'quantity',
        'condition_note',
    ];
        protected $casts = [
        'loan_item_id' => 'string',
        'return_id'  => 'string',
        'book_id'  => 'string',
    ];


    /**
     * Relasi ke loan item
     */
    public function loanItem(): BelongsTo
    {
        return $this->belongsTo(LoanItem::class);
    }

    /**
     * Relasi ke return
     */
    public function return(): BelongsTo
    {
        return $this->belongsTo(Returns::class);
    }

    /**
     * Relasi ke book
     */
    public function book(): BelongsTo
    {
        return $this->belongsTo(Books::class);
    }

    
}
