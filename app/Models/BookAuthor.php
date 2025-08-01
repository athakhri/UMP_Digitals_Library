<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookAuthor extends Model
{
  use HasUlids;
    
    protected $fillable =[
    'book_id',
    'author_id'
    ];

    protected $table = 'book_author';

    protected function casts(): array
    {
        return [
            'book_id' => 'string',
            'author_id' => 'string',
        ];
    }

    public function book():BelongsTo{
        return $this->belongsTo(Book::class, 'user_id');
    }

    public function author():BelongsTo{
        return $this->belongsTo(Author::class, 'author_id');
    }
}
