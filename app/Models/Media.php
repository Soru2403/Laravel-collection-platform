<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    use HasFactory;

    protected $table = 'media';
    protected $fillable = ['title', 'description', 'type', 'creator', 'release_year', 'genre', 'image_url'];

    // Attiecība ar kolekcijām (daudz pret daudziem)
    public function collections()
    {
        return $this->belongsToMany(Collection::class, 'collection_media');
    }
}