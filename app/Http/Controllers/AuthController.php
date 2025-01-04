<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    // Rāda reģistrācijas formas lapu
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    // Apstrādā reģistrācijas pieprasījumu
    public function register(Request $request)
    {
        // Validē lietotāja ievadi
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Izveido jaunu lietotāju
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        // Pieslēdz lietotāju uzreiz pēc reģistrācijas
        Auth::login($user);

        // Pāradresē uz profila rediģēšanas lapu pēc reģistrācijas
        return redirect()->route('profile.complete_registration_form')->with('success', 'Reģistrācija veiksmīga! Lūdzu, pabeidziet savu profilu.');
    }

    // Rāda pieteikšanās formas lapu
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Apstrādā pieteikšanās pieprasījumu
    public function login(Request $request)
    {
        // Validē ievadītos datus
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        // Saņem lietotāja ievadītās autentifikācijas datnes
        $credentials = $request->only('email', 'password');

        // Pārbauda autentifikāciju un pieslēdz lietotāju, ja dati ir pareizi
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate(); // Atjauno sesiju drošībai
            return redirect()->route('profile')->with('success', 'Veiksmīgi pieslēdzāties!');
        }

        // Atgriež kļūdu, ja autentifikācija neizdevās
        return back()->withErrors([
            'email' => 'Jūs ievadījāt nepareizu e-pasta adresi vai paroli.',
        ])->onlyInput('email');
    }

    // Izraksta lietotāju no sistēmas
    public function logout(Request $request)
    {
        Auth::logout(); // Izraksta lietotāju

        // Dzēš sesiju un atjauno sesijas tokenu
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Pāradresē uz sākumlapu
        return redirect()->route('home')->with('success', 'Esat veiksmīgi izrakstījies!');
    }
}