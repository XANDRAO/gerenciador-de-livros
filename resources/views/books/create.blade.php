@extends('layouts.app')

@section('content')
    <h1>Criar Livro</h1>
    <form action="{{ route('books.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <!-- Campos do formulário -->
        <div class="form-group">
            <label for="title">Título</label>
            <input type="text" name="title" id="title" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="author_id">Autor</label>
            <select name="author_id" id="author_id" class="form-control" required>
                @foreach($authors as $author)
                    <option value="{{ $author->id }}">{{ $author->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="publisher">Editora</label>
            <input type="text" name="publisher" id="publisher" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="publication_year">Ano de Publicação</label>
            <input type="text" name="publication_year" id="publication_year" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="pages_amount">Número de Páginas</label>
            <input type="text" name="pages_amount" id="pages_amount" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="isbn_number">ISBN</label>
            <input type="text" name="isbn_number" id="isbn_number" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="synopsis">Sinopse</label>
            <textarea name="synopsis" id="synopsis" class="form-control" rows="3"></textarea>
        </div>
        <div class="form-group">
            <label for="file">Arquivo PDF</label>
            <input type="file" name="file" id="file" class="form-control">
        </div>
        <div class="form-group">
            <label for="image">Imagem</label>
            <input type="file" name="image" id="image" class="form-control">
        </div>
        <button type="submit" class="btn btn-primary">Adicionar Livro</button>
    </form>
@endsection
