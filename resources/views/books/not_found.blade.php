@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h1 class="mb-4">Livro Não Encontrado</h1>
    <p>O livro com o título ou ISBN fornecido não foi encontrado.</p>
    <a href="{{ route('books.index') }}" class="btn btn-secondary mt-3">Voltar à Lista</a>
</div>
@endsection
