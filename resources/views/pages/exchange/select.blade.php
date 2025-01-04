@extends('layouts.app')

@section('content')
<div class="back-button mb-3">
    <a href="{{ url()->previous() }}" class="btn btn-secondary">Atpakaļ</a>
</div>
<div class="container">
    <h3>Izvēlieties savu kolekciju, lai mainītu ar: {{ $targetCollection->title }}</h3>

    @if ($userCollections->isEmpty())
        <p>Jums nav nevienas kolekcijas, ko apmainīt.</p>
    @else
        <form action="{{ route('exchange.store') }}" method="POST">
            @csrf
            <input type="hidden" name="target_collection_id" value="{{ $targetCollection->id }}">

            <div class="form-group">
                <label for="user_collection_id">Jūsu kolekcijas:</label>
                <select id="user_collection_id" name="user_collection_id" class="form-control">
                    @foreach ($userCollections as $collection)
                        <option value="{{ $collection->id }}">{{ $collection->title }}</option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="btn btn-primary mt-3">Piedāvāt apmaiņu</button>
        </form>
    @endif
</div>
@endsection

