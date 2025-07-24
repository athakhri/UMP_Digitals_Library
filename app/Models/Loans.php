<?php

namespace App\Models;

use App\Models\User;
use App\Models\LoanItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Loans extends Model
{
    use HasUlids;
    
    protected $fillable = [
        'user_id',
        'loan_date',
        'status'
    ];

    protected $table = 'loans';

    protected function casts(): array
    {
        return [
            'user_id'   => 'string',
            'loan_date' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function loanItems(): HasMany
    {
        return $this->hasMany(LoanItem::class, 'loan_id');
    }
}
