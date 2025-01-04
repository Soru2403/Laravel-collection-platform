@extends('layouts.app')

@section('content')
    <div class="container">
        {{-- Poga atgriezties iepriekšējā lapā --}}
        <div class="back-button mb-3">
            <a href="{{ url()->previous() }}" class="btn btn-secondary">Atpakaļ</a>
        </div>
        <h1>Izveidot jaunu kolekciju</h1>

        <form action="{{ route('collections.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="title">Kolekcijas nosaukums</label>
                <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" required>
                @error('title')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="description">Apraksts</label>
                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4">{{ old('description') }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="tag">Atslēgvārds</label>
                <input type="text" class="form-control @error('tag') is-invalid @enderror" id="tag" name="tag" value="{{ old('tag') }}" required>
                @error('tag')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="privacy">Privātums</label>
                <select class="form-control @error('privacy') is-invalid @enderror" id="privacy" name="privacy" required>
                    <option value="public" {{ old('privacy') == 'public' ? 'selected' : '' }}>Publiska</option>
                    <option value="friends" {{ old('privacy') == 'friends' ? 'selected' : '' }}>Draugi</option>
                    <option value="private" {{ old('privacy') == 'private' ? 'selected' : '' }}>Privāta</option>
                </select>
                @error('privacy')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="media">Izvēlieties mediju, ko pievienot kolekcijai:</label>
                <div class="media-selection row">
                    {{-- Caur visiem pieejamajiem medijiem un attēlo tos --}}
                    @foreach ($mediaList as $media)
                        <div class="col-md-4 mb-4">
                            <div class="card h-100">
                                <label>
                                    <input type="checkbox" name="media[]" value="{{ $media->id }}" 
                                        {{ in_array($media->id, old('media', [])) ? 'checked' : '' }}>
                                    {{-- Ja attēls ir pieejams, rādām to --}}
                                    @if ($media->image_url)
                                        <img src="{{ $media->image_url }}" alt="{{ $media->title }}" class="card-img-top">
                                    @else
                                        <div class="card-body">
                                            <p class="card-text">{{ $media->title }}</p>
                                        </div>
                                    @endif
                                </label>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                {{-- Pievieno kļūdas ziņojumu, ja mediji nav izvēlēti --}}
                @error('media')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary">Izveidot kolekciju</button>
        </form>
    </div>
@endsection




