@extends('layouts.app')

@section('content')
    <div class="container">

        <h1>{{ $book->title }}</h1>

        <p>Autor: {{ optional($book->author)->name ?? 'Desconhecido' }}</p>
        
        <p>Editora: {{ $book->publisher }}</p>

        <p>Ano de Publicação: {{ $book->publication_year }}</p>

        <p>Número de Páginas: {{ $book->pages_amount }}</p>

        <p>ISBN: {{ $book->isbn_number }}</p>
        
        @if(!empty($book->cover_url))
                <img src="{{ $book->cover_url }}" alt="Capa do Livro" style="width: 200px;">
        @endif

        <p>Sinopse: {{ $book->synopsis }}</p>

        @if(!empty($book->file_url))
            <a target="_blank" href="{{ $book->file_url }}" class="btn btn-primary" download>Baixar Arquivo</a>
        @endif

        <a href="{{ route('books.search', ['query' => request('query')]) }}" class="btn btn-secondary">Voltar para Pesquisa</a>

    </div>
@endsection
