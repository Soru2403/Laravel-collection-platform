@extends('layouts.app')

@section('content')
<div class="collection-details">
    {{-- Poga atgriezties iepriekšējā lapā --}}
    <div class="back-button mb-3">
        <a href="{{ url()->previous() }}" class="btn btn-secondary">Atpakaļ</a>
    </div>
    
    {{-- Pārbauda, vai kolekcija pastāv --}}
    @if ($collection)
        {{-- Augšējā sadaļa ar kolekcijas informāciju --}}
        <div class="collection-header">
            <div class="row">
                <div class="col-md-8">
                    <h2>{{ $collection->title }}</h2>
                    <p><strong>Apraksts:</strong> {{ $collection->description ?? 'Nav pieejams apraksts' }}</p>
                    <p><strong>Atslēgvārdi:</strong> {{ $collection->tag ?? 'Nav pieejami tagi' }}</p>
                    <p><strong>Autors:</strong> <a href="{{ route('profile.show', $collection->user_id) }}">{{ $collection->user->name }}</a></p>
                </div>
                <div class="col-md-4 text-end">
                    {{-- Parādīt pogas tikai kolekcijas autoram --}}
                    <div class="collection-actions">
                        {{-- Rediģēt kolekciju --}}
                        @if(auth()->check() && auth()->id() === $collection->user_id)
                            <a href="{{ route('collections.edit', $collection->id) }}" class="btn btn-secondary">Rediģēt</a>
                        @endif
                        {{-- Dzēst kolekciju --}}
                        @if(auth()->check() && (auth()->user()->id === $collection->user_id || auth()->user()->role === 'admin'))
                            <form action="{{ route('collections.destroy', $collection->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Vai tiešām vēlaties dzēst šo kolekciju?')">Dzēst</button>
                            </form>
                        @endif 
                        {{-- Ja lietotājs nav kolekcijas īpašnieks, parādīt iespēju uzsākt apmaiņu --}}
                        @if (auth()->check() && auth()->id() !== $collection->user_id)
                            <div class="exchange-section">
                                <a href="{{ route('exchange.select', $collection->id) }}" class="btn btn-primary">Piedāvāt apmaiņu</a>
                            </div>
                        @endif   
                    </div>
                </div>
            </div>
        </div>

        {{-- Horizontālā līnija starp augšējo un apakšējo sadaļu --}}
        <hr>

        {{-- Apakšējā sadaļa ar medijiem --}}
        <div class="media-section">
            <h3>Mediji šajā kolekcijā:</h3>
            @if ($collection->media->isEmpty())
                <p>Šai kolekcijai nav pievienoti mediji.</p>
            @else
                {{-- Attēlojām mediju kolekciju ar kartēm --}}
                <div class="media-gallery row">
                    @foreach ($collection->media as $media)
                        <div class="col-md-4 mb-4">
                            <div class="card h-100">
                                {{-- Ja attēls ir pieejams, rādām to, citādi parādam nosaukumu --}}
                                @if ($media->image_url)
                                    <a href="{{ route('media.show', $media->id) }}" class="media-link">
                                        <img src="{{ $media->image_url }}" alt="{{ $media->title }}" class="card-img-top">
                                    </a>
                                @else
                                    <div class="card-body">
                                        <a href="{{ route('media.show', $media->id) }}" class="card-title">{{ $media->title }}</a>
                                    </div>
                                @endif
                                <div class="card-body">
                                    <p class="card-text"><strong>Veids:</strong> {{ $media->type }}</p>
                                    <p class="card-text"><strong>Žanrs:</strong> {{ $media->genre }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Rādām vidējo vērtējumu un formu vērtēšanai --}}
        @if ($collection->privacy === 'public')
            <div class="rating-section">
                {{-- Vidējā kolekcijas vērtējuma attēlošana --}}
                <p><strong>Vidējāis vērtējums:</strong> {{ $averageRating ?? 'Nav vērtējumu' }} ({{ $collection->ratings->count() }} vērtējumi)</p>

                {{-- Formas vērtēšanai un vērtējuma noņemšanai --}}
                @if (auth()->check() && auth()->id() !== $collection->user_id)
                    <form action="{{ route('collections.rate', $collection->id) }}" method="POST">
                        @csrf
                        <label for="rating">Novērtējums:</label>
                        <div class="rating-bar">
                            @for ($i = 1; $i <= 5; $i++)
                                <label>
                                    <input type="radio" name="rating" value="{{ $i }}" 
                                        {{ old('rating', $userRating->rating ?? '') == $i ? 'checked' : '' }}>
                                    {{ $i }}
                                </label>
                            @endfor
                        </div>
                        <div class="rating-actions mt-3">
                            {{-- Poga vērtējuma iesniegšanai --}}
                            <button type="submit" class="btn btn-primary">Iesniegt vērtējumu</button>
                        </div>
                    </form>

                    {{-- Poga vērtējuma noņemšanai --}}
                    @if ($userRating)
                        <form action="{{ route('collections.remove_rating', $collection->id) }}" method="POST" class="mt-3">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-secondary"
                                onclick="return confirm('Vai tiešām vēlaties noņemt savu vērtējumu?')">Noņemt vērtējumu</button>
                        </form>
                    @endif
                @endif
            </div>
        @endif
    @else
        <p>Kolekcija nav atrasta vai tai nav piekļuves.</p>
    @endif
</div>
@endsection