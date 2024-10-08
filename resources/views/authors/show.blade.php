@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h1 class="mb-4">Detalhes do Autor</h1>

    <div class="card">
        <div class="card-header">
            <h5 class="card-title">{{ $author->name }}</h5>
        </div>
        <div class="card-body">
            <p><strong>Endereço:</strong> {{ $author->street_address }}</p>
            <p><strong>Cidade:</strong> {{ $author->city }}</p>
            <p><strong>Estado:</strong> {{ $author->state }}</p>
            <p><strong>País:</strong> {{ $author->country }}</p>

            @if($author->picture_url)
            <img src="{{ $author->picture_url }}" alt="{{ $author->name }}" class="img-thumbnail w-25">
            @endif

            <a href="{{ route('authors.edit', $author->id) }}" class="btn btn-warning mt-3">Editar</a>
            <a href="{{ route('authors.index') }}" class="btn btn-secondary mt-3">Voltar a Lista</a>
        </div>
    </div>
</div>
@endsection
