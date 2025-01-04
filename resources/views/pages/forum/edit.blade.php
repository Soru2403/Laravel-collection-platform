@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <h1>Rediģēt ierakstu</h1>

            <!-- Formas sākums -->
            <form method="POST" action="{{ route('forum.update', $post->id) }}">
                @csrf
                @method('PUT') 
                
                <div class="mb-3">
                    <label for="title" class="form-label">Nosaukums</label>
                    <input type="text" class="form-control" id="title" name="title" value="{{ old('title', $post->title) }}" required>
                </div>

                <div class="mb-3">
                    <label for="keywords" class="form-label">Atslēgvārdi</label>
                    <input type="text" class="form-control" id="keywords" name="keywords" value="{{ old('keywords', $post->keywords) }}">
                </div>

                <div class="mb-3">
                    <label for="content" class="form-label">Saturs</label>
                    <textarea class="form-control" id="content" name="content" rows="5" required>{{ old('content', $post->content) }}</textarea>
                </div>

                <button type="submit" class="btn btn-primary">Saglabāt izmaiņas</button>
            </form>
        </div>
    </div>
</div>
@endsection
