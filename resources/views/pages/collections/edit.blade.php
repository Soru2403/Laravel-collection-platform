@extends('layouts.app')

@section('content')
<div class="edit-collection">
    <h2>Rediģēt kolekciju: {{ $collection->title }}</h2>

    <form action="{{ route('collections.update', $collection->id) }}" method="POST">
        @csrf
        @method('PUT')

        {{-- Kolekcijas nosaukums --}}
        <div class="form-group">
            <label for="title">Nosaukums:</label>
            <input type="text" id="title" name="title" class="form-control" value="{{ $collection->title }}" required>
        </div>

        {{-- Kolekcijas apraksts --}}
        <div class="form-group">
            <label for="description">Apraksts:</label>
            <textarea id="description" name="description" class="form-control">{{ $collection->description }}</textarea>
        </div>

        {{-- Privātuma iestatījumi --}}
        <div class="form-group">
            <label for="privacy">Privātums:</label>
            <select id="privacy" name="privacy" class="form-control">
                <option value="public" {{ $collection->privacy === 'public' ? 'selected' : '' }}>Publiska</option>
                <option value="friends" {{ $collection->privacy === 'friends' ? 'selected' : '' }}>Draugiem</option>
                <option value="private" {{ $collection->privacy === 'private' ? 'selected' : '' }}>Privāta</option>
            </select>
        </div>

        {{-- Esošie mediji --}}
        <div class="form-group">
            <label for="existing_media">Esošie mediji kolekcijā:</label>
            <div class="row media-selection">
                @foreach ($collection->media as $media)
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                        <img src="{{ $media->image_url }}" alt="{{ $media->title }}" class="card-img-top">
                            <div class="card-body">
                                <h5 class="card-title">{{ $media->title }}</h5>
                                <p class="card-text"><strong>Veids:</strong> {{ $media->type }}</p>
                                <p class="card-text"><strong>Žanrs:</strong> {{ $media->genre }}</p>
                                <label>
                                    <input type="checkbox" name="existing_media[]" value="{{ $media->id }}" checked>
                                    Izvēlēties
                                </label>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Pieejamie mediji --}}
        <div class="form-group">
            <label for="media">Izvēlieties mediju, ko pievienot kolekcijai:</label>
            <div class="row media-selection">
                {{-- Caur visiem pieejamajiem medijiem un attēlo tos --}}
                @foreach ($mediaList as $media)
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                        <img src="{{ $media->image_url }}" alt="{{ $media->title }}" class="media-image">
                            <div class="card-body">
                                <h5 class="card-title">{{ $media->title }}</h5>
                                <p class="card-text"><strong>Veids:</strong> {{ $media->type }}</p>
                                <p class="card-text"><strong>Žanrs:</strong> {{ $media->genre }}</p>
                                <label>
                                    <input type="checkbox" name="media[]" value="{{ $media->id }}" 
                                        {{ in_array($media->id, old('media', [])) ? 'checked' : '' }}>
                                    Izvēlēties
                                </label>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Saglabāšanas poga --}}
        <button type="submit" class="btn btn-primary">Saglabāt izmaiņas</button>
    </form>
</div>
@endsection