@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Lista de Livros</h1>
        
        <!-- Botão de Adicionar Livro -->
        <a href="{{ route('books.create') }}" class="btn btn-success mb-4">Adicionar Livro</a>
        
        <!-- Formulário de Pesquisa -->
        <form action="{{ route('books.search') }}" method="GET" class="mb-4">
            <input type="text" name="query" placeholder="Pesquisar livros" class="form-control" required>
            <button type="submit" class="btn btn-primary mt-2">Pesquisar</button>
        </form>
        
        @if (count($books) > 0)
            <table class="table">
                <thead>
                    <tr>
                        <th>Título</th>
                        <th>Autor</th>
                        <th>Editora</th>
                        <th>Ano de Publicação</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($books as $book)
                    <tr>
                        <td>{{ $book->title }}</td>
                        <td>{{ $book->author->name ?? 'Desconhecido' }}</td>
                        <td>{{ $book->publisher }}</td>
                        <td>{{ $book->publication_year }}</td>
                        <td>
                            <a href="{{ route('books.show', $book->id) }}" class="btn btn-info">Visualizar</a>
                            <a href="{{ route('books.edit', $book->id) }}" class="btn btn-warning">Editar</a>
                            <form action="{{ route('books.destroy', $book->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Tem certeza que deseja excluir este livro?')">Excluir</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <!-- Paginação -->
            {{ $books->links() }}
            @else
            <p>Nenhum livro encontrado.</p>
            @endif
        </div>
        @endsection
