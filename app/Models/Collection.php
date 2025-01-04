<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Collection extends Model
{
    // Atļautie lauki masveida piešķiršanai
    protected $fillable = [
        'title', 
        'description', 
        'tag', 
        'user_id', 
        'privacy', 
        'average_rating'
    ];

    // Attiecības ar lietotāju (kolekcijas īpašnieks)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Attiecības ar medijiem
    public function media()
    {
        return $this->belongsToMany(Media::class, 'collection_media');
    }

    // Attiecības ar kolekciju vērtējumiem
    public function ratings()
    {
        return $this->hasMany(CollectionRating::class);
    }

    // Funkcija vidējā vērtējuma iegūšanai
    public function calculateAverageRating()
    {
        $average = $this->ratings()->avg('rating');
        $this->average_rating = $average;
        $this->save();
        return $average;
    }

    // Metode medija pievienošanai kolekcijai
    public function addMedia($mediaId)
    {
        return $this->media()->attach($mediaId); // Saista kolekciju ar mediju
    }

    // Metode medija noņemšanai no kolekcijas
    public function removeMedia($mediaId)
    {
        return $this->media()->detach($mediaId); // Noņem mediju no kolekcijas
    }
}


