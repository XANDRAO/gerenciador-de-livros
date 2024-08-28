<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Author extends Model
{
    use HasFactory;

    // Campos atribuíveis em massa
    protected $fillable = [
        'name',
        'street_address',
        'city',
        'state',
        'country',
        'picture_url',
    ];
}
