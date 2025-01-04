@extends('layouts.app')

@section('content')
{{-- Horizontāla sadaļa ar lietotāja informāciju --}}
<div class="user-info-section">
    <h2>
        @if (Auth::check() && Auth::id() === $user->id)
            Mans profils
        @else
            {{ $user->name }}'s profils
        @endif
    </h2>
    
    {{-- Ja ir ziņojums par veiksmīgu darbību --}}
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    
    <div class="profile-info">
        <p><strong>Vārds:</strong> {{ $user->name }}</p>
        <p><strong>Apraksts:</strong> {{ $user->user_description }}</p>
        <p><strong>Reģistrācijas datums:</strong> {{ $user->created_at->format('Y-m-d') }}</p>
    </div>
    <div class="profile-actions">
        {{-- Pārbauda, vai lietotājs ir profila īpašnieks vai administrators --}}
        @if (Auth::check() && (Auth::id() === $user->id || auth()->user()->role === 'admin'))
            {{-- Poga profila dzēšanai --}}
            <form action="{{ route('profile.destroy', $user->id) }}" method="POST" style="display: inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger" onclick="return confirm('Vai tiešām vēlaties dzēst šo profilu?')">Dzēst profilu</button>
            </form>
        @endif
        
        {{-- Poga rediģēt profilu --}}
        @if (Auth::check() && Auth::id() === $user->id)
            <a href="{{ route('profile.edit') }}" class="btn btn-secondary">Rediģēt profilu</a>
            <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                @csrf
                <button type="submit" class="btn btn-danger">Izrakstīties</button>
            </form>
        @endif
        {{-- Administratora paneļa saite --}}
        @if (Auth::check() && Auth::id() === $user->id && auth()->user()->role === 'admin')
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-primary me-2">Administratora panelis</a>
        @endif
    </div>
</div>

{{-- Sadalīšana uz divām vertikālām sadaļām --}}
<div class="row mt-4">
    {{-- Kreisais stabs - draugu saraksts --}}
    <div class="col-md-6">
        {{-- Pārbauda, vai lietotājs var sūtīt draudzības pieprasījumu --}}
        @if (Auth::check() && Auth::id() !== $user->id)
            @php
                $friendship = auth()->user()->friendships()->where('friend_id', $user->id)->first();
                $isFriend = auth()->user()->friendsList->contains($user);
            @endphp

            {{-- Ja lietotāji vēl nav draugi --}}
            @if (!$friendship && !$isFriend)
                <form action="{{ route('friendships.send') }}" method="POST">
                    @csrf
                    <input type="hidden" name="friend_id" value="{{ $user->id }}">
                    <button type="submit" class="btn btn-success">Pievienot draugiem</button>
                </form>
            {{-- Ja draudzības pieprasījums ir "gaida" statusā --}}
            @elseif ($friendship && $friendship->status === 'pending')
                <p>Gaida apstiprinājumu</p>
            {{-- Ja lietotāji jau ir draugi --}}
            @elseif ($isFriend)
                <p>Jūs esat draugi!</p>
            @endif
        @endif

        {{-- Parāda saņemtos draudzības pieprasījumus --}}
        @if (Auth::check() && Auth::id() === $user->id && $user->receivedFriendRequests->isNotEmpty())
            <h3>Saņemtie draudzības pieprasījumi</h3>
            <table class="table">
                <thead>
                    <tr>
                        <th>Vārds</th>
                        <th>Darbība</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($user->receivedFriendRequests as $request)
                        <tr>
                            <td><a href="{{ route('profile.show', $request->user->id) }}">{{ $request->user->name }}</a></td>
                            <td>
                                {{-- Poga pieprasījuma pieņemšanai --}}
                                <form action="{{ route('friendships.accept', $request->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-success">Pieņemt</button>
                                </form>
                                {{-- Poga pieprasījuma noraidīšanai --}}
                                <form action="{{ route('friendships.destroy', $request->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">Noraidīt</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        {{-- Parāda draugu sarakstu --}}
        <h3>Draugi</h3>
        @if ($user->friendsList->isNotEmpty())
            <table class="table">
                <thead>
                    <tr>
                        <th>Vārds</th>
                        @if (Auth::check() && Auth::id() === $user->id)
                            <th>Darbība</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach ($user->friendsList as $friend)
                        <tr>
                            <td><a href="{{ route('profile.show', $friend->id) }}">{{ $friend->name }}</a></td>
                            <td>
                                {{-- Poga draudzības dzēšanai --}}
                                @if (Auth::check() && Auth::id() === $user->id)
                                    <form action="{{ route('friendships.destroy', $friend->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="redirect_back" value="{{ url()->current() }}">
                                        <button type="submit" class="btn btn-danger" onclick="return confirm('Vai tiešām vēlaties dzēst šo draugu?')">Dzēst no draugiem</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p>Nav draugu.</p>
        @endif
    </div>

    {{-- Labais stabs - kolekcijas --}}
    <div class="col-md-6">
        <h3>
            @if (Auth::check() && Auth::id() === $user->id)
                Manas kolekcijas
            @else
                {{ $user->name }}'s kolekcijas
            @endif
        </h3>
        {{-- Poga kolekcijas pievienošanai, pieejama tikai pašam lietotājam --}}
        @if (Auth::check() && Auth::id() === $user->id)
            <a href="{{ route('collections.create') }}" class="btn btn-primary mb-3">Izveidot jaunu kolekciju</a>
        @endif
        
        {{-- Kolekciju saraksts --}}
        @foreach ($user->collections as $collection)
            @if ($user->id === auth()->id() || 
                auth()->check() && (auth()->user()->role === 'admin' || 
                $collection->privacy === 'public' || 
                ($collection->privacy === 'friends' && auth()->user()->friendsList->contains($collection->user_id)) ||
                !auth()->check()) )
                <div class="collection-box">
                    <a href="{{ route('collections.show', $collection->id) }}" class="collection-link">
                        <div class="collection-title">
                            {{ $collection->title }} ({{ $collection->media->count() }})
                        </div>
                    </a>
                </div>
            @endif
        @endforeach

        {{-- Ja nav redzamu kolekciju --}}
        @if ($user->collections->isEmpty())
            <p>Kolekciju nav pieejamu.</p>
        @endif
    </div>
</div>
@endsection
