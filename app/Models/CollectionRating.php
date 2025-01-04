<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CollectionRating extends Model
{
    use HasFactory;

    protected $table = 'collection_ratings';

    protected $fillable = [
        'collection_id',
        'user_id',
        'rating',
    ];

    // Attiecība ar kolekciju
    public function collection()
    {
        return $this->belongsTo(Collection::class);
    }

    // Attiecība ar lietotāju
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}


