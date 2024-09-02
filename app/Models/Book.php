<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'title',
        'author_id',
        'publisher',
        'publication_year',
        'pages_amount',
        'isbn_number',
        'file_url',
        'image_name',
        'synopsis',
    ];
    public function author()
{
    return $this->belongsTo(Author::class);
}

}
