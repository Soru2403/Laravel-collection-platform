<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    // Administratora panelis
    public function adminDashboard()
    {
        // Pārbaudām, vai lietotājs ir administrators
        if (!Auth::user()->isAdmin()) {
            return redirect()->route('home')->with('error', 'Jums nav piekļuves administrācijas panelim!');
        }
    
        // Atgriež visu lietotāju sarakstu administrēšanai
        $users = User::paginate(10); // Lapas izmērs: 10 ieraksti
        return view('admin.dashboard', compact('users'));
    }

    // Dzēš lietotāju
    public function destroy($id)
    {
        // Pārbaudām, vai lietotājs pastāv datubāzē
        $user = User::findOrFail($id);
    
        // Pārbaudām, vai pašreizējais lietotājs ir administrators vai pats savs profila īpašnieks
        if (Auth::id() !== $user->id && !Auth::user()->isAdmin()) {
            return redirect()->route('home')->with('error', 'Jums nav tiesību dzēst šo profilu!');
        }
    
        // Ja pašreizējais lietotājs mēģina dzēst pats sevi
        if (Auth::id() === $user->id) {
            $user->delete(); // Dzēš lietotāju no datubāzes
            Auth::logout(); // Izraksta lietotāju no sistēmas
    
            return redirect()->route('home')->with('success', 'Jūsu profils veiksmīgi dzēsts.');
        }
    
        // Ja administrators dzēš citu lietotāju
        $user->delete();
    
        return redirect()->route('admin.dashboard')->with('success', 'Lietotājs veiksmīgi dzēsts.');
    }
    
    // Lietotāja profils
    public function profile()
    {
        // Atgriež skatu ar pašreizējā lietotāja datiem
        return view('users.profile', ['user' => Auth::user()]);
    }

    // Rāda cita lietotāja profilu pēc ID
    public function show($id)
    {
        // Atrodam lietotāju pēc ID
        $user = User::findOrFail($id);
    
        // Iniciējam $receivedRequests kā null
        $receivedRequests = null;
    
        // Pārbaudām, vai pašreizējais lietotājs skatās savu profilu
        if (auth()->check() && auth()->id() === $id) {
            // Iegūstam visus saņemtos draudzības pieprasījumus ar lietotāju datiem
            $receivedRequests = auth()->user()->receivedFriendRequests()->with('user')->get();
        }
    
        // Atgriežam skatu ar lietotāja un pieprasījumu datiem
        return view('users.profile', [
            'user' => $user,                   // Lietotāja informācija
            'receivedRequests' => $receivedRequests, // Saņemtie draudzības pieprasījumi
        ]);
    }

    // Rāda formu profila rediģēšanai
    public function edit()
    {
        // Iegūst pašreizējo autentificēto lietotāju
        $user = Auth::user();
    
        // Atgriež skatu ar rediģēšanas formu
        return view('users.edit', compact('user'));
    }

    // Atjaunina lietotāja profilu
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
    
        // Validē ievades laukus
        $request->validate([
            'name' => 'required|string|max:20',  // Vārds ar līdz 20 rakstzīmēm
            'password' => 'nullable|string|max:50|confirmed',  // Parole, ne obligāta, līdz 50 rakstzīmēm
            'description' => 'nullable|string|max:5000',  // Apraksts, līdz 5000 rakstzīmēm
        ]);
    
        // Atjaunina lietotāja vārdu
        $user->name = $request->name;
    
        // Ja parole ir ievadīta, to atjauno
        if ($request->filled('password')) {
            $user->password = bcrypt($request->password);
        }
    
        // Saglabā aprakstu
        $user->user_description = $request->description;
    
        // Saglabā izmaiņas
        $user->save(); 

        // Paziņojums par veiksmīgu profila atjaunināšanu
        return redirect()->route('profile')->with('success', 'Profils veiksmīgi atjaunināts!');
    }

    // Lietotāja sākumlapa
    public function home()
    {
        // Atgriež lietotāja sākumlapas skatu
        $user = Auth::user();
        return view('users.home', compact('user'));
    }
        public function showCompleteRegistrationForm()
    {
        // Rāda lapu, kur var pabeigt reģistrāciju
        return view('profile.complete_registration');
    }
    public function completeRegistration(Request $request)
    {
        // Validē ievadi
        $request->validate([
            'name' => 'required|string|max:20',
            'user_description' => 'nullable|string|max:5000',
        ]);
    
        // Atjauno lietotāja datus
        $user = auth()->user();
    
        // Atjaunina lietotāja vārdu
        $user->name = $request->name;
    
        // Saglabā aprakstu (pareizais lauka nosaukums no formas)
        $user->user_description = $request->user_description;
    
        // Saglabā izmaiņas
        $user->save(); 
    
        // Pāradresē uz profila lapu
        return redirect()->route('profile')->with('success', 'Reģistrācija pabeigta!');
    }    
}
