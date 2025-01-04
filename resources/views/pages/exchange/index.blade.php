@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Visi pieejamie apmaiņas pieprasījumi</h3>

    @forelse ($exchanges as $exchange)
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">
                    <a href="{{ route('exchange.show', $exchange->id) }}">{{ $exchange->collection1->title }} <i class="fas fa-exchange-alt"></i> {{ $exchange->collection2->title }}</a>
                </h5>
                <p class="card-text">Statuss: {{ ucfirst($exchange->status) }}</p>
            </div>
        </div>
    @empty
        <p>Nav pieejamu apmaiņu.</p>
    @endforelse
</div>
@endsection

