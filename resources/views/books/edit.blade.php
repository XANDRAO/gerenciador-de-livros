@extends('layouts.app')

@section('title', 'Editar Livro')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3>Editar Livro</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('books.update', $book->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- Título -->
                <div class="mb-3">
                    <label for="title" class="form-label">Título</label>
                    <input type="text" class="form-control" id="title" name="title" value="{{ old('title', $book->title) }}" required>
                </div>

                <!-- Autor -->
                <div class="mb-3">
                    <label for="author_id" class="form-label">Autor</label>
                    <select class="form-select" id="author_id" name="author_id" required>
                        @foreach ($authors as $author)
                            <option value="{{ $author->id }}" {{ $author->id == old('author_id', $book->author_id) ? 'selected' : '' }}>{{ $author->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Editora -->
                <div class="mb-3">
                    <label for="publisher" class="form-label">Editora</label>
                    <input type="text" class="form-control" id="publisher" name="publisher" value="{{ old('publisher', $book->publisher) }}" required>
                </div>

                <!-- Ano de Publicação -->
                <div class="mb-3">
                    <label for="publication_year" class="form-label">Ano de Publicação</label>
                    <input type="number" class="form-control" id="publication_year" name="publication_year" value="{{ old('publication_year', $book->publication_year) }}" required>
                </div>

                <!-- Número de Páginas -->
                <div class="mb-3">
                    <label for="pages_amount" class="form-label">Número de Páginas</label>
                    <input type="number" class="form-control" id="pages_amount" name="pages_amount" value="{{ old('pages_amount', $book->pages_amount) }}" required>
                </div>

                <!-- ISBN -->
                <div class="mb-3">
                    <label for="isbn_number" class="form-label">ISBN</label>
                    <input type="text" class="form-control" id="isbn_number" name="isbn_number" value="{{ old('isbn_number', $book->isbn_number) }}" required>
                </div>
                
                <!-- Sinopse -->
                <div class="form-group">
                    <label for="synopsis">Sinopse</label>
                    <textarea name="synopsis" id="synopsis" class="form-control" rows="3">{{ old('synopsis', $book->synopsis) }}</textarea>
                </div>

                <!-- Arquivo PDF -->
                <div class="mb-3">
                    <label for="file" class="form-label">Arquivo PDF</label>
                    <input type="file" class="form-control" id="file" name="file" accept=".pdf">
                    @if($book->file_url)
                        <a href="{{ Storage::url($book->file_url) }}" target="_blank">Visualizar PDF Atual</a>
                    @endif
                </div>

                <!-- Imagem -->
                <div class="mb-3">
                    <label for="image" class="form-label">Imagem da Capa</label>
                    <input type="file" class="form-control" id="image" name="image" accept="image/*">
                    @if($book->image_url)
                        <img src="{{ $book->image_url }}" alt="{{ $book->title }}" style="width: 100px; margin-top: 10px;">
                    @endif
                </div>
                <button type="submit" class="btn btn-custom">Atualizar Livro</button>
            </form>
        </div>
    </div>
@endsection
