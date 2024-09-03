@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Deletar livro</h1>
        <div class="alert alert-danger">
            <p>Tem certeza de que deseja excluir o livro intitulado "{{ $book->title }}"?</p>
        </div>
        <form action="{{ route('books.destroy', $book->id) }}" method="POST">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">Deletar</button>
            <a href="{{ route('books.index') }}" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
@endsection
