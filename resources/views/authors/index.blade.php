@extends('layouts.app')

@section('content')
    <h1>Lista de Autores</h1>
    <a href="{{ route('authors.create') }}" class="btn btn-primary">Adicionar Autor</a>

    @if ($authors->isEmpty())
        <p>Nenhum autor encontrado.</p>
    @else
        <table class="table">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Endereço</th>
                    <th>Cidade</th>
                    <th>Estado</th>
                    <th>País</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($authors as $author)
                    <tr>
                        <td>{{ $author->name }}</td>
                        <td>{{ $author->street_address }}</td>
                        <td>{{ $author->city }}</td>
                        <td>{{ $author->state }}</td>
                        <td>{{ $author->country }}</td>
                        <td>
                            <a href="{{ route('authors.edit', $author->id) }}" class="btn btn-warning">Editar</a>
                            <form action="{{ route('authors.destroy', $author->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">Excluir</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{ $authors->links() }} <!-- Paginação -->
    @endif
@endsection
