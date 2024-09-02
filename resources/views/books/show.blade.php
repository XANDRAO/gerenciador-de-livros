@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>{{ $book->title }}</h1>
        <p>Autor: {{ is_object($book->author) ? $book->author->name : 'Desconhecido' }}</p>
        <p>Editora: {{ $book->publisher }}</p>
        <p>Ano de Publicação: {{ $book->publication_year }}</p>
        <p>Número de Páginas: {{ $book->pages_amount }}</p>
        <p>ISBN: {{ $book->isbn_number }}</p>
        @if($book->image_url)
            <img src="{{ $book->image_url }}" alt="Capa do Livro" style="width: 200px;">
        @endif
        <p>Sinopse: {{ $book->synopsis }}</p>
        @if(isset($book->file_url))
            <a href="{{ route('books.download', $book->id) }}" class="btn btn-primary">Baixar Arquivo</a>
        @endif
        <a href="{{ route('books.search', ['query' => request('query')]) }}" class="btn btn-secondary">Voltar para Pesquisa</a>
    </div>
@endsection
