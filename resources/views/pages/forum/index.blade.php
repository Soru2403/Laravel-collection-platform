@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <!-- Kreisais stabiņš -->
        <div class="col-md-3">
            <!-- Poga jauna ieraksta izveidei -->
            @auth
                <a href="{{ route('forum.create') }}" class="btn btn-primary btn-block mb-3">Izveidot jaunu ierakstu</a>
            @endauth

            <!-- Meklēšanas forma -->
            <form method="GET" action="{{ route('forum.search') }}" class="mb-3">
                <div class="input-group">
                    <!-- Meklēšanas ievades lauks -->
                    <input type="text" name="query" class="form-control" placeholder="Meklēt pēc nosaukuma/atslēgvārdiem" value="{{ request('query') }}">
                    <button type="submit" class="btn btn-secondary">Meklēt</button>
                </div>
            </form>

            <!-- Ja ir meklēšanas teksts, rādām pogu, kas dzēš meklēšanas tekstu -->
            @if(request('query'))
                <form method="GET" action="{{ route('forum.search') }}" class="mt-3">
                    <button type="submit" class="btn btn-danger">Notīrīt meklēšanu</button>
                </form>
            @endif
        </div>

        <!-- Labais stabiņš -->
        <div class="col-md-9">
            <!-- Ierakstu kārtošanas izvēlne -->
            <div class="d-flex justify-content-end mb-3">
                <form method="GET" action="{{ route('forum.index') }}">
                    <!-- Slēpti lauki meklēšanas vaicājumam, lai saglabātu to -->
                    <input type="hidden" name="query" value="{{ request('query') }}">
                    <select name="sort" onchange="this.form.submit()" class="form-select w-auto">
                        <!-- Opcijas kārtošanas virzienam -->
                        <option value="desc" {{ request('sort') == 'desc' ? 'selected' : '' }}>Jaunākie</option>
                        <option value="asc" {{ request('sort') == 'asc' ? 'selected' : '' }}>Vecākie</option>
                    </select>
                </form>
            </div>

            <!-- Ierakstu saraksts -->
            @if($posts->isEmpty())
                <p class="text-muted">Nav atrasti ieraksti.</p>
            @else
                <div class="list-group">
                    <!-- Cikls cauri ierakstiem -->
                    @foreach($posts as $post)
                        <a href="{{ route('forum.show', $post->id) }}" class="list-group-item list-group-item-action">
                            <h5>{{ $post->title }}</h5>
                            <p class="mb-1 text-muted">{{ Str::limit($post->content, 100) }}</p>
                            <small>
                                Izveidoja: <a href="{{ route('profile.show', $post->user->id) }}">{{ $post->user->name }}</a>
                                - {{ $post->created_at->format('d.m.Y H:i') }}
                            </small>
                        </a>
                    @endforeach
                </div>
                <!-- Lapu navigācija -->
                <div class="mt-3">
                    <!-- Atjaunots links ar pieprasījuma parametriem, kas ļauj saglabāt meklēšanas un kārtošanas parametrus -->
                    {{ $posts->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection










