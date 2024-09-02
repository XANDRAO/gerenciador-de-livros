@extends('layouts.app')

@section('content')
    <h1>Criar Autor</h1>

    <form action="{{ route('authors.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="name">Nome</label>
            <input type="text" name="name" id="name" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="bio">Bio</label>
            <textarea name="bio" id="bio" class="form-control" rows="3" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Salvar</button>
    </form>
@endsection
