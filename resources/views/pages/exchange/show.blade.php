@extends('layouts.app')

@section('content')
<div class="container">
    <div class="back-button mb-3">
        <a href="{{ url()->previous() }}" class="btn btn-secondary">Atpakaļ</a>
    </div>
    <h3>Apmaiņas informācija</h3>

    <p><strong>Apmaiņas statuss:</strong> {{ $exchange->status }}</p>

    <p><strong>Lietotājs, kurš piedāvāja pirmo kolekciju:</strong> 
        <a href="{{ route('profile.show', $exchange->user1->id) }}">
            {{ $exchange->user1->name }}
        </a>
    </p>

    <p><strong>Izvēlētā kolekcija no lietotāja 1:</strong> 
        <a href="{{ route('collections.show', $exchange->collection1->id) }}">
            {{ $exchange->collection1->title }}
        </a>
    </p>

    <p><strong>Otrā lietotāja, kurš piedāvāja otro kolekciju:</strong> 
        <a href="{{ route('profile.show', $exchange->user2->id) }}">
            {{ $exchange->user2->name }}
        </a>
    </p>

    <p><strong>Izvēlētā kolekcija no lietotāja 2:</strong> 
        <a href="{{ route('collections.show', $exchange->collection2->id) }}">
            {{ $exchange->collection2->title }}
        </a>
    </p>

    {{-- Ja lietotājs ir piedāvātājs, neskatām pieņemšanas un noraidīšanas pogas --}}
    @if(auth()->id() !== $exchange->user_id_1)
        @if($exchange->status === 'pending')
            <form action="{{ route('exchange.accept', $exchange->id) }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-success">Pieņemt apmaiņu</button>
            </form>

            <form action="{{ route('exchange.reject', $exchange->id) }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-danger">Noraidīt apmaiņu</button>
            </form>
        @endif
    @endif
</div>
@endsection


