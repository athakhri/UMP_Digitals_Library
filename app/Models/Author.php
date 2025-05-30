<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Author extends Model
{
    
    use HasUlids;
    
    protected $fillable =[
    'name',
    'nationality',
    'birthdate'
    ];

    protected $table = 'author';

    protected function casts(): array
    {
        return [
            'name' => 'string',
            'nationality' => 'string',
            'birthdate' => 'string',
        ];
    }


    public function BookAuthor():HasMany{
        return $this->hasMany(BookAuthor::class,'author_id');
    }
}