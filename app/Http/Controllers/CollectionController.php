<?php

namespace App\Http\Controllers;

use App\Models\Collection;
use App\Models\Media;
use App\Models\CollectionRating;
use Illuminate\Http\Request;

class CollectionController extends Controller
{
    // Metode kolekciju saraksta attēlošanai
    public function index(Request $request)
    {
        // Iegūst pieprasījuma parametrus
        $search = $request->input('search'); // Meklēšanas parametru
        $filter = $request->input('filter', 'latest'); // Filtrs (pēc noklusējuma "jaunākās")
    
        // Sākotnējā pieprasījuma sagatavošana kolekciju atlasei, ņemot vērā tikai publiskās kolekcijas
        $query = Collection::where('privacy', 'public'); // Skatām tikai publiskās kolekcijas
    
        // Pievieno meklēšanas kritērijus, ja ir ievadīts meklēšanas teksts
        if ($search) {
            $query->where(function ($q) use ($search) {
                // Meklē pēc nosaukuma vai atslēgas vārdiem
                $q->where('title', 'LIKE', '%' . $search . '%')
                  ->orWhere('tag', 'LIKE', '%' . $search . '%');
            });
        }
    
        // Pielieto filtru, pamatojoties uz izvēlēto parametru
        if ($filter === 'latest') {
            // Jaunākās kolekcijas (pēc izveidošanas datuma)
            $query->orderBy('created_at', 'desc');
        } elseif ($filter === 'popular') {
            // Populārākās kolekcijas (pēc vērtējumu skaita)
            $query->withCount('ratings') // Skaita vērtējumus katrai kolekcijai
                  ->orderBy('ratings_count', 'desc');
        } elseif ($filter === 'highest') {
            // Kolekcijas ar augstāko vidējo vērtējumu
            $query->withAvg('ratings', 'rating') // Aprēķina vidējo vērtējumu
                  ->orderBy('ratings_avg', 'desc');
        }
    
        // Atlasa kolekcijas ar saistītajiem autoriem un medijiem
        // Pievienojam lapošanu ar 6 kolekcijām uz vienu lapu
        $collections = $query->with(['user', 'media'])->paginate(6);
    
        // Atgriež skatu ar kolekcijām un papildinformāciju
         return view('pages.collections.index', compact('collections'));
    }
    
    // Lietotāja kolekcijas izveidošanas forma
    public function create()
    {
        // Iegūst visus pieejamos medijus, kurus lietotājs var izvēlēties pievienot kolekcijai
        $mediaList = Media::all();
        
        // Atgriež skatu ar kolekcijas izveides formu un pieejamiem medijiem
        return view('pages.collections.create', compact('mediaList'));
    }

    // Kolekcijas saglabāšana datubāzē
    public function store(Request $request)
    {
        // Validācija kolekcijas datiem
        $validated = $request->validate([
            'title' => 'required|string|max:20',  
            'description' => 'nullable|string|max:200',  
            'tag' => 'required|string|max:20',  
            'privacy' => 'required|in:public,friends,private',  
            'media' => 'required|array|min:1',  
            'media.*' => 'exists:media,id', 
        ], [
            // Pielāgotie ziņojumi
            'title.required' => 'Lūdzu ievadiet kolekcijas nosaukumu.',
            'title.max' => 'Nosaukums pārsniedz atļauto simbolu skaitu.',
            'tag.required' => 'Lūdzu ievadiet kolekcijas atslēgvārdu.',
            'tag.max' => 'Atslēgvārdam ir jābūt ne vairāk kā 20 rakstzīmēm.',
            'media.required' => 'Vajag pievienot vismaz vienu mediju.',
            'media.min' => 'Lūdzu izvēlieties vismaz vienu mediju.',
            'media.*.exists' => 'Izvēlētais medijs nav derīgs.',
        ]);
    
        // Kolekcijas izveide
        $collection = new Collection();
        $collection->title = $validated['title'];
        $collection->description = $validated['description'] ?? null;
        $collection->tag = $validated['tag'] ?? null;
        $collection->privacy = $validated['privacy'];
        $collection->user_id = auth()->id(); // Saista ar pašreizējo lietotāju
        $collection->save();
    
        // Pievieno medijus kolekcijai
        $collection->media()->attach($validated['media']);
    
        return redirect()->route('profile.show', auth()->id())->with('success', 'Kolekcija veiksmīgi izveidota!');
    }
    
    // Parāda konkrētu kolekciju
    public function show($id)
    {
        // Iegūst kolekciju, pievienojot tās saistītos medijus
        $collection = Collection::with('media')->findOrFail($id);
    
    // Iegūst pašreizējo lietotāja vērtējumu kolekcijai, ja lietotājs ir pieslēdzies
    $userRating = null;
    if (auth()->check()) {
        // Meklē vērtējumu, kas piešķirts šai kolekcijai no pašreizējā lietotāja
        $userRating = CollectionRating::where('collection_id', $collection->id)
                                    ->where('user_id', auth()->id())
                                    ->first();
    }

    // Iegūst kolekcijas vidējo vērtējumu
    $averageRating = CollectionRating::where('collection_id', $collection->id)->avg('rating');

    // Noapaļo vidējo vērtējumu līdz vienam ciparam pēc komata
    $averageRating = round($averageRating, 1);
    
        // Atgriež skatu ar kolekcijas, lietotāja vērtējuma un vidējā vērtējuma datiem
        return view('pages.collections.show', compact('collection', 'userRating', 'averageRating'));
    }
    
    // Atver rediģēšanas formu konkrētai kolekcijai.
    public function edit(Collection $collection)
    {
        $mediaList = Media::all();

        return view('pages.collections.edit', compact('collection', 'mediaList'));
    }

    // Atjaunina kolekcijas datus.
    public function update(Request $request, Collection $collection)
    {
        // Validējam ievades datus.
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'privacy' => 'required|in:public,friends,private',
            'media.*' => 'nullable|exists:media,id',
            'existing_media.*' => 'nullable|exists:media,id',
        ]);
    
        // Atjaunojam kolekcijas informāciju.
        $collection->update($request->only('title', 'description', 'privacy'));
    
        // Atjaunojam kolekcijai pievienotos medijus.
        $selectedMedia = array_merge($request->input('media', []), $request->input('existing_media', []));
        $collection->media()->sync($selectedMedia);
    
        return redirect()->route('collections.show', $collection->id)->with('success', 'Kolekcija veiksmīgi atjaunota!');
    }

    // Dzēš konkrēto kolekciju.
    public function destroy(Collection $collection)
    {
        $collection->delete(); // Dzēšam kolekciju no datubāzes.

        // Pārvirzām lietotāju atpakaļ uz profila lapu ar ziņojumu par veiksmīgu dzēšanu.
        return redirect()->route('profile.show', auth()->id())->with('success', 'Kolekcija veiksmīgi dzēsta!');
    }

    // Reģistrē lietotāja vērtējumu kolekcijai
    public function rate(Request $request, Collection $collection)
    {
        // Validējam vērtējuma ievadi, lai nodrošinātu, ka tas ir no 1 līdz 5
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',  // Vērtējums no 1 līdz 5
        ]);

        // Pārbauda, vai lietotājs jau ir novērtējis šo kolekciju
        $userRating = CollectionRating::where('collection_id', $collection->id)
            ->where('user_id', auth()->id())
            ->first();

        if ($userRating) {
            // Ja jau ir vērtējums, atjaunojam to
            $userRating->update(['rating' => $request->rating]);
        } else {
            // Ja vēl nav vērtējuma, pievienojam jaunu
            CollectionRating::create([
                'user_id' => auth()->id(),
                'collection_id' => $collection->id,
                'rating' => $request->rating,
            ]);
        }

        // Atgriežam atpakaļ uz kolekcijas lapu ar ziņu par veiksmīgu vērtējumu
        return redirect()->route('collections.show', $collection->id)
            ->with('success', 'Jūsu vērtējums ir saglabāts!');
    }

    public function removeRating(Collection $collection)
    {
        // Pārbauda, vai lietotājs ir pieslēdzies
        if (auth()->check()) {
            // Atrod un dzēš lietotāja vērtējumu šai kolekcijai
            CollectionRating::where('collection_id', $collection->id)
                ->where('user_id', auth()->id())
                ->delete();
        }

        // Atgriež uz kolekcijas lapu ar paziņojumu
        return redirect()->route('collections.show', $collection->id)
            ->with('success', 'Jūsu vērtējums ir noņemts!');
    }

}

