<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\ReturnItem;

class Returns extends Model
{
    use HasUlids;

    protected $table = 'returns'; // eksplisitkan agar tetap jelas

    protected $fillable = [
        'loan_id',
        'return_date',
        'notes',
    ];

    protected $casts = [
        'loan_id' => 'string',
        'return_date'  => 'date',
    ];

    /**
     * Relasi: pengembalian ini milik satu item pinjaman
     */
    public function loan(): BelongsTo
    {
        return $this->belongsTo(Loans::class);
    }

        public function returnItems()
    {
        return $this->hasMany(ReturnItem::class, 'return_id' ); // Pastikan nama model ReturnItem benar
    }
}
