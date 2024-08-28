<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Author extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'street_address',
        'city',
        'state',
        'country',
        'picture_url'
    ];

    /**
     * Relacionamento: Um autor pode ter muitos livros.
     */
    public function books()
    {
        return $this->hasMany(Book::class);
    }
}
