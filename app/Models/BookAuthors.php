<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookAuthors extends Model
{
    use HasUlids;
    
    protected $fillable =[
    'book_id',
    'author_id'
    ];

    protected $table = 'book_authors';

    protected function casts(): array
    {
        return [
            'book_id' => 'string',
            'author_id' => 'string',
        ];
    }

    public function book():BelongsTo{
        return $this->belongsTo(Books::class, 'book_id');
    }

    public function author():BelongsTo{
        return $this->belongsTo(Authors::class, 'author_id');
    }
}
