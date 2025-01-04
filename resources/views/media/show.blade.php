@extends('layouts.app')

@section('content')
<div class="back-button mb-3">
    <a href="{{ url()->previous() }}" class="btn btn-secondary">Atpakaļ</a>
</div>
<h2>{{ $media->title }}</h2>

<div class="row">
    {{-- Kreisais kolonna: Attēls --}}
    <div class="col-md-4">
        <img src="{{ $media->image_url }}" alt="{{ $media->title }}" class="img-fluid rounded shadow">
        {{-- Administrators var rediģēt vai dzēst mēdiju --}}
        @if (auth()->check() && auth()->user()->role === 'admin')
            <div class="mt-3">
                <a href="{{ route('media.edit', $media->id) }}" class="btn btn-primary">Rediģēt</a>
                <form action="{{ route('media.destroy', $media->id) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" onclick="return confirm('Vai tiešām vēlaties dzēst šo mēdiju?')">Dzēst</button>
                </form>
            </div>
        @endif
    </div>

    {{-- Labā kolonna: Informācija --}}
    <div class="col-md-8">
        <ul class="list-group">
            {{-- Parāda informāciju par mēdiju --}}
            <li class="list-group-item"><strong>Nosaukums:</strong> {{ $media->title }}</li>
            <li class="list-group-item"><strong>Veids:</strong> {{ ucfirst($media->type) }}</li>
            <li class="list-group-item"><strong>Apraksts:</strong> {{ $media->description }}</li>
            <li class="list-group-item"><strong>Radītājs:</strong> {{ $media->creator }}</li>
            <li class="list-group-item"><strong>Žanrs:</strong> {{ $media->genre }}</li>
            <li class="list-group-item"><strong>Izdošanas gads:</strong> {{ $media->release_year }}</li>
        </ul>
    </div>
</div>
@endsection
