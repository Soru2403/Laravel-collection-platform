<!-- resources/views/media/index.blade.php -->
@extends('layouts.app')

@section('content')
<h2>Mēdiju saraksts</h2>
<div class="mt-3">
    {{-- Administrators var pievienot jaunu mēdiju --}}
    @if (auth()->check() && auth()->user()->role === 'admin')
        <a href="{{ route('media.create') }}" class="btn btn-success mt-3">Pievienot jaunu mēdiju</a>
    @endif
</div>

<div class="row">
    {{-- Kreisais kolonna: Filtrācija --}}
    <div class="col-md-3">
        <form action="{{ route('media.index') }}" method="GET" class="mb-3">
            <h4>Filtrēt pēc veida</h4>
            <div class="list-group">
                <a href="{{ route('media.index') }}" class="list-group-item {{ request('type') === null ? 'active' : '' }}">
                    Visi
                </a>
                <a href="{{ route('media.index', ['type' => 'spēle']) }}" class="list-group-item {{ request('type') === 'spēle' ? 'active' : '' }}">
                    Spēles
                </a>
                <a href="{{ route('media.index', ['type' => 'filma']) }}" class="list-group-item {{ request('type') === 'filma' ? 'active' : '' }}">
                    Filmas
                </a>
                <a href="{{ route('media.index', ['type' => 'seriāls']) }}" class="list-group-item {{ request('type') === 'seriāls' ? 'active' : '' }}">
                    Seriāli
                </a>
                <a href="{{ route('media.index', ['type' => 'grāmata']) }}" class="list-group-item {{ request('type') === 'grāmata' ? 'active' : '' }}">
                    Grāmatas
                </a>
            </div>
        </form>
    </div>

    {{-- Labā kolonna: Mēdiju saraksts --}}
    <div class="col-md-9">
        {{-- Ja nav mēdiju, parāda ziņojumu --}}
        @if ($media->isEmpty())
            <p>Mēdiji šajā kategorijā pašlaik nav pieejami.</p>
        @else
            <div class="row">
                @foreach ($media as $item)
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <img src="{{ $item->image_url }}" class="card-img-top" alt="{{ $item->title }}">
                            <div class="card-body">
                                <h5 class="card-title">{{ $item->title }}</h5>
                                <p class="card-text"><strong>Veids:</strong> {{ $item->type }}</p>
                                <p class="card-text"><strong>Žanrs:</strong> {{ $item->genre }}</p>
                                <a href="{{ route('media.show', $item->id) }}" class="btn btn-info">Skatīt</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
