@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Resultados da Pesquisa</h1>
        @if (count($books) > 0)
            <table class="table">
                <thead>
                    <tr>
                        <th>Título</th>
                        <th>Autor</th>
                        <th>Editora</th>
                        <th>Ano de Publicação</th>
                        <th>Capa</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($books as $book)
                        <tr>
                            <td>{{ $book->title }}</td>
                            <td>{{ $book->author ?? 'Desconhecido' }}</td>
                            <td>{{ $book->publisher }}</td>
                            <td>{{ $book->publication_year }}</td>
                            <td>
                                @if($book->image_url)
                                    <img src="{{ $book->image_url }}" alt="Capa do Livro" style="width: 100px;">
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('books.show', $book->id) }}" class="btn btn-info">Visualizar</a>

                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p>Nenhum livro encontrado com a sua pesquisa.</p>
        @endif
        <a href="{{ route('books.index') }}" class="btn btn-secondary">Voltar para Lista</a>
    </div>
@endsection
