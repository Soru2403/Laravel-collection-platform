<?php

namespace App\Http\Controllers;

use App\Models\Exchange;
use App\Models\Collection;
use Illuminate\Http\Request;

class ExchangeController extends Controller
{
    // Kolekcijas apmaiņas poga
    public function select($collectionId)
    {
        // Iegūstam lietotāja kolekcijas
        $userCollections = auth()->user()->collections;

        // Iegūstam kolekciju, ar kuru lietotājs vēlas mainīties
        $targetCollection = Collection::findOrFail($collectionId);

        // Atgriežam skatu ar kolekciju izvēli
        return view('pages.exchange.select', compact('userCollections', 'targetCollection'));
    }

    // Apmaiņas izveide pēc kolekcijas izvēles
    public function store(Request $request)
    {
        // Pārbaudām, vai lietotājs izvēlējās kolekciju
        $userCollection = Collection::findOrFail($request->user_collection_id);
        $targetCollection = Collection::findOrFail($request->target_collection_id);

        // Izveidojam jaunu apmaiņu
        $exchange = Exchange::create([
            'user_id_1' => auth()->id(),
            'user_id_2' => $targetCollection->user_id,
            'collection_id_1' => $userCollection->id,
            'collection_id_2' => $targetCollection->id,
            'status' => 'pending',
        ]);

        // Pārsūtām lietotāju uz apmaiņas informācijas skatu
        return redirect()->route('exchange.show', $exchange->id)
            ->with('success', 'Apmaiņa ir izveidota!');
    }

    // Apmaiņas skatīšanai
    public function show($id)
    {
        // Iegūstam apmaiņas informāciju
        $exchange = Exchange::findOrFail($id);

        // Atgriežam skatu ar apmaiņas informāciju
        return view('pages.exchange.show', compact('exchange'));
    }

    // Skatīt visus apmaiņas pieprasījumus
    public function index()
    {
        // Iegūstam visus apmaiņas pieprasījumus, kuros piedalās pašreizējais lietotājs
        $exchanges = Exchange::where('user_id_1', auth()->id())
                             ->orWhere('user_id_2', auth()->id())
                             ->get();

        // Atgriežam skatu ar apmaiņas pieprasījumiem
        return view('pages.exchange.index', compact('exchanges'));
    }

    // Pieņem apmaiņas pieprasījumu
    public function accept($id)
    {
        $exchange = Exchange::findOrFail($id);

        // Mainām apmaiņas statusu uz "accepted" (pieņemts)
        $exchange->update(['status' => 'accepted']);

        // Mainām kolekciju piederību, lai lietotāji veiktu apmaiņu
        $exchange->collection1()->update(['user_id' => $exchange->user_id_2]);
        $exchange->collection2()->update(['user_id' => $exchange->user_id_1]);

        return redirect()->route('exchange.index')
            ->with('success', 'Apmaiņa pieņemta!');
    }

    // Noraida apmaiņas pieprasījumu
    public function reject($id)
    {
        $exchange = Exchange::findOrFail($id);

        // Mainām apmaiņas statusu uz "rejected" (noraidīts)
        $exchange->update(['status' => 'rejected']);

        return redirect()->route('exchange.index')
            ->with('error', 'Apmaiņa noraidīta!');
    }
}





