@extends('layouts.app')

@section('title', 'Editar Autor')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3>Editar Autor</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('authors.update', $author->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- Nome -->
                <div class="mb-3">
                    <label for="name" class="form-label">Nome</label>
                    <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $author->name) }}" required>
                </div>

                <!-- Endereço -->
                <div class="mb-3">
                    <label for="street_address" class="form-label">Endereço</label>
                    <input type="text" class="form-control" id="street_address" name="street_address" value="{{ old('street_address', $author->street_address) }}">
                </div>

                <!-- Cidade -->
                <div class="mb-3">
                    <label for="city" class="form-label">Cidade</label>
                    <input type="text" class="form-control" id="city" name="city" value="{{ old('city', $author->city) }}">
                </div>

                <!-- Estado -->
                <div class="mb-3">
                    <label for="state" class="form-label">Estado</label>
                    <input type="text" class="form-control" id="state" name="state" value="{{ old('state', $author->state) }}">
                </div>

                <!-- País -->
                <div class="mb-3">
                    <label for="country" class="form-label">País</label>
                    <input type="text" class="form-control" id="country" name="country" value="{{ old('country', $author->country) }}">
                </div>

                <!-- CEP -->
                <div class="mb-3">
                    <label for="cep" class="form-label">CEP</label>
                    <input type="text" class="form-control" id="cep" name="cep" value="{{ old('cep', $author->cep) }}" maxlength="9">
                </div>

                <!-- Imagem -->
                <div class="mb-3">
                    <label for="picture" class="form-label">Imagem do Autor</label>
                    <input type="file" class="form-control" id="picture" name="picture" accept="image/*">
                    @if($author->picture_url)
                        <img src="{{ $author->picture_url }}" alt="{{ $author->name }}" style="width: 100px; margin-top: 10px;">
                    @endif
                </div>

                <button type="submit" class="btn btn-custom">Atualizar Autor</button>
            </form>
        </div>
    </div>
@endsection
