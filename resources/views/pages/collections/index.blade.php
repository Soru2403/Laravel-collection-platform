@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            {{-- Kreisais kolonns --}}
            <div class="col-md-4">
                {{-- Poga, lai izveidotu jaunu kolekciju --}}
                @if(auth()->check())
                    <a href="{{ route('collections.create') }}" class="btn btn-primary mb-3">Izveidot jaunu kolekciju</a>
                @endif

                <!-- Meklēšanas forma -->
                <form method="GET" action="{{ route('collections.index') }}">
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Meklēt kolekcijas..." aria-label="Meklēt kolekcijas">
                        <button class="btn btn-outline-secondary" type="submit" id="button-search">Meklēt</button>
                    </div>
                </form>

                <!-- Ja ir meklēšanas teksts, rādām pogu, kas dzēš meklēšanas tekstu -->
                @if(request('search'))
                    <form method="GET" action="{{ route('collections.index') }}">
                        <button type="submit" class="btn btn-danger mt-3">Notīrīt meklēšanu</button>
                    </form>
                @endif
            </div>

            {{-- Labais kolonns --}}
            <div class="col-md-8">
                {{-- Filtrēšanas opcijas --}}
                <div class="d-flex justify-content-end mb-3">
                    <form action="{{ route('collections.index') }}" method="GET" class="d-inline">
                        <label for="filter" class="me-2">Filtrēt:</label>
                        <select name="filter" id="filter" class="form-select form-select-sm w-auto d-inline" 
                            onchange="this.form.submit()">
                            <option value="latest" {{ request('filter') == 'latest' ? 'selected' : '' }}>Jaunākās</option>
                            <option value="popular" {{ request('filter') == 'popular' ? 'selected' : '' }}>Populārākās</option>
                        </select>
                    </form>
                </div>

                {{-- Kolekciju saraksts --}}
                @if($collections->isEmpty())
                    <p>Nav pieejamu publisku kolekciju.</p>
                @else
                    <div class="row">
                        @foreach($collections as $collection)
                            <div class="col-md-6 mb-4">
                                <div class="card h-100">
                                    <a href="{{ route('collections.show', $collection->id) }}" class="text-decoration-none">
                                        <div class="card-body">
                                            {{-- Kolekcijas nosaukums --}}
                                            <h5 class="card-title">{{ $collection->title }}</h5>

                                            {{-- Kolekcijas autors --}}
                                            <p><strong>Autors:</strong> 
                                                <a href="{{ route('profile.show', $collection->user_id) }}">
                                                    {{ $collection->user->name }}
                                                </a>
                                            </p>

                                            {{-- Atslēgvārdi --}}
                                            <p><strong>Atslēgvārdi:</strong> {{ $collection->tag ?? 'Nav' }}</p>

                                            {{-- Apraksts --}}
                                            <p class="card-text">{{ \Illuminate\Support\Str::limit($collection->description, 100) }}</p>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Pārlūkošanas pogas (paginācija) --}}
                    <div class="d-flex justify-content-center mt-4">
                        {{ $collections->links() }} {{-- Laravel automātiskā lapošanas metode --}}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection


