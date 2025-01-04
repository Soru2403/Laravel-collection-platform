<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\ForumPost;
use App\Models\Friendship;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }


    //Saņem visus lietotāja forumu ierakstus.
    public function forumPosts()
    {
        return $this->hasMany(ForumPost::class);
    }


    //Pārbauda, vai lietotājs ir administrators.
    public function isAdmin()
    {
        return $this->role === 'admin';  // Ja 'role' ir 'admin', atgriežam true, citādi false
    }


    //Lietotāja nosūtītie draudzības pieprasījumi.
    public function sentFriendRequests()
    {
        return $this->hasMany(Friendship::class, 'user_id')->where('status', 'pending');
    }


    //Lietotāja saņemtie draudzības pieprasījumi.
    public function receivedFriendRequests()
    {
        // Saņem visus draudzības pieprasījumus, kas adresēti šim lietotājam un atrodas gaidīšanas statusā
        return $this->hasMany(Friendship::class, 'friend_id')->where('status', 'pending');
    }

    //Lietotāja draudzības attiecības.
    public function friends()
    {
        return $this->hasMany(Friendship::class, 'user_id')
                    ->where('status', 'accepted')
                    ->orWhere(function ($query) {
                        $query->where('friend_id', $this->id)
                              ->where('status', 'accepted');
                    });
    }
    
    //Draudzības attiecības (nosūtīti un saņemti pieprasījumi).
    public function friendships()
    {
        // Šis attiecība norāda visus draudzības pieprasījumus, ko lietotājs ir nosūtījis vai saņēmis
        return $this->hasMany(Friendship::class, 'user_id')
                    ->orWhere('friend_id', $this->id);
    }

    //Palīdzīga funkcija, lai pārbaudītu, vai lietotājam ir draudzība ar citu lietotāju.
    public function isFriendWith($friendId)
    {
        return $this->friends()->where(function($query) use ($friendId) {
            $query->where('user_id', $friendId)
                  ->orWhere('friend_id', $friendId);
        })->exists();
    }


    //Palīdzīga funkcija, lai pārbaudītu, vai lietotājam ir nosūtīts draudzības pieprasījums.
    public function hasSentRequest($friendId)
    {
        return $this->sentFriendRequests()->where('friend_id', $friendId)->exists();
    }

    //Palīdzīga funkcija, lai pārbaudītu, vai lietotājam ir saņemts draudzības pieprasījums.
    public function hasReceivedRequest($friendId)
    {
        return $this->receivedFriendRequests()->where('user_id', $friendId)->exists();
    }


    //Draudzības saraksts, kuru lietotājs ir pieņēmis.
    public function getFriendsListAttribute()
    {
        return User::whereIn('id', function ($query) {
            $query->select('friend_id')
                ->from('friendships')
                ->where('user_id', $this->id)
                ->where('status', 'accepted')
                ->union(
                    $query->newQuery()
                            ->select('user_id')
                            ->from('friendships')
                            ->where('friend_id', $this->id)
                            ->where('status', 'accepted')
                );
        })->get();
    }
    public function collections()
    {
    return $this->hasMany(Collection::class);
    }
    public function exchanges()
    {
    return $this->hasMany(Exchange::class, 'user_id_1'); // или 'user_id_2', в зависимости от того, где используется id
    }
    public function exchangesAsUser1()
    {
        return $this->hasMany(Exchange::class, 'user_id_1');
    }
    
    public function exchangesAsUser2()
    {
        return $this->hasMany(Exchange::class, 'user_id_2');
    }
}