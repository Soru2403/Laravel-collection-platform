<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Friendship;

class FriendshipController extends Controller
{

     //Nosūta draudzības pieprasījumu.
    public function sendRequest(Request $request)
    {
        // Validē ievades datus - pārbauda, vai friend_id pastāv datubāzē
        $request->validate(['friend_id' => 'required|exists:users,id']);

        // Pārbauda, vai lietotājs mēģina nosūtīt draudzības pieprasījumu pats sev
        if ($request->friend_id == auth()->id()) {
            return back()->withErrors(['error' => 'Jūs nevarat nosūtīt draudzības pieprasījumu pašam sev.']);
        }

        // Pārbauda, vai draudzības pieprasījums jau nepastāv
        if (auth()->user()->sentFriendRequests()->where('friend_id', $request->friend_id)->exists()) {
            return back()->withErrors(['error' => 'Draudzības pieprasījums jau nosūtīts.']);
        }

        // Izveido jaunu draudzības pieprasījumu
        auth()->user()->sentFriendRequests()->create(['friend_id' => $request->friend_id]);

        return back()->with('success', 'Draudzības pieprasījums veiksmīgi nosūtīts!');
    }


    //Pieņem draudzības pieprasījumu.
    public function acceptRequest($id)
    {
        // Pārbaudām, vai pašreizējais lietotājs ir saņēmis šo draudzības pieprasījumu
        // Atrodam saņemto draudzības pieprasījumu pēc ID
        $friendship = auth()->user()->receivedFriendRequests()->find($id);
    
        // Ja draudzības pieprasījums nav atrasts (tas varētu būt, ja ir kaut kāds kļūdains ID)
        if (!$friendship) {
            // Atgriežam kļūdas ziņojumu, ja pieprasījums netika atrasts
            return back()->withErrors(['error' => 'Draudzības pieprasījums netika atrasts.']);
        }
    
        // Ja draudzības pieprasījums jau ir apstiprināts vai noraidīts, tad neko nedarām
        if ($friendship->status === 'accepted') {
            return back()->withErrors(['error' => 'Draudzības pieprasījums jau ir apstiprināts.']);
        }
    
        // Atjaunojam draudzības pieprasījuma statusu uz 'accepted'
        $friendship->update([
            'status' => 'accepted',        // Izmainām statusu uz apstiprinātu
            'accepted_at' => now(),        // Pievienojam laiku, kad pieprasījums tika apstiprināts
        ]);
    
        // Atgriežam atpakaļ ar veiksmīgu ziņojumu
        return back()->with('success', 'Draudzības pieprasījums veiksmīgi pieņemts!');
    }
    
    //Dzēš draudzības pieprasījumu vai attiecības.
    public function destroy($id)
    {
        // Mēs meklējam draudzības attiecību starp pašreizējo lietotāju un lietotāju ar ID $id
        $friendship = Friendship::where(function($query) use ($id) {
            // Pārbaudām, vai attiecība ir starp pašreizējo lietotāju un draugu
            $query->where('user_id', auth()->user()->id)
                  ->where('friend_id', $id);
        })
        ->orWhere(function($query) use ($id) {
            // Pārbaudām, vai attiecība ir starp draugu un pašreizējo lietotāju
            $query->where('user_id', $id)
                  ->where('friend_id', auth()->user()->id);
        })
        ->first();
    
        // Ja draudzības attiecība netika atrasta
        if (!$friendship) {
            return back()->with('error', 'Šī draudzība netika atrasta.');
        }
    
        // Dzēšam draudzības attiecību
        $friendship->delete();
    
        // Atgriežam lietotāju atpakaļ uz iepriekšējo lapu ar veiksmīgu ziņojumu
        return back()->with('success', 'Draudzība tika dzēsta.');
    }    
}