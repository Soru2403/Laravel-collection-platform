<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exchange extends Model
{
    use HasFactory;

    // Norādām, kuras kolonnas var tikt piešķirtas masveidā
    protected $fillable = [
        'user_id_1', // Lietotājs, kurš uzsāk apmaiņu
        'user_id_2', // Lietotājs, kuram tiek piedāvāta apmaiņa
        'collection_id_1', // Kolekcija, kuru lietotājs vēlas iegūt
        'collection_id_2', // Kolekcija, kuru lietotājs piedāvā apmaiņai
        'status', // Apmaiņas statuss: pending, accepted, rejected
    ];


    //Attiecība uz pirmo lietotāju (tas, kurš uzsāk apmaiņu)
    public function user1()
    {
        return $this->belongsTo(User::class, 'user_id_1');
    }

    
    //Attiecība uz otro lietotāju (tas, kuram tiek piedāvāta apmaiņa)
    public function user2()
    {
        return $this->belongsTo(User::class, 'user_id_2');
    }

    // Attiecība uz kolekciju, kuru lietotājs vēlas iegūt
    public function collection1()
    {
        return $this->belongsTo(Collection::class, 'collection_id_1');
    }


    // Attiecība uz kolekciju, kuru lietotājs piedāvā apmaiņai
    public function collection2()
    {
        return $this->belongsTo(Collection::class, 'collection_id_2');
    }


    //Pārbauda, vai apmaiņa ir pieņemta
    public function isAccepted()
    {
        return $this->status === 'accepted';
    }


    //Pārbauda, vai apmaiņa ir noraidīta
    public function isRejected()
    {
        return $this->status === 'rejected';
    }


    //Pārbauda, vai apmaiņa ir gaidoša
    public function isPending()
    {
        return $this->status === 'pending';
    }
}


