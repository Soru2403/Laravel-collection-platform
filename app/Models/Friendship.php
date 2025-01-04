<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Friendship extends Model
{
    use HasFactory;

    // Aizpildāmās lauki, kas norāda, kuras vērtības var tikt piešķirtas masīva veidā
    protected $fillable = ['user_id', 'friend_id', 'status', 'accepted_at'];


    //Saistība ar lietotāju (pieprasījuma sūtītājs).
    public function user()
    {
        // Atgriež saistību ar lietotāju, kurš ir sūtījis draudzības pieprasījumu
        return $this->belongsTo(User::class, 'user_id');
    }

    //Saistība ar lietotāju (draugs).
    public function friend()
    {
        // Atgriež saistību ar lietotāju, kurš ir draugs
        return $this->belongsTo(User::class, 'friend_id');
    }
}

