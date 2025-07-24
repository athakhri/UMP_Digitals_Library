<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Authors extends Model
{
    
    use HasUlids;
    
    protected $fillable =[
    'name',
    'nationality',
    'birthdate'
    ];

    protected $table = 'authors';

    protected function casts(): array
    {
        return [
            'name' => 'string',
            'nationality' => 'string',
            'birthdate' => 'string',
        ];
    }

    // public function stock():HasMany{
    //     return $this->hasMany(Stock::class,'barang_id');
    // }

    public function order():HasMany{
        return $this->hasMany(BookAuthors::class,'author_id');
    }
}