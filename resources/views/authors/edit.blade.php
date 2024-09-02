@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Editar Autor</h1>
        <form action="{{ route('authors.update', $author->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="name">Nome</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $author->name) }}" required>
            </div>

            <div class="form-group">
                <label for="cep">CEP</label>
                <input type="text" name="cep" id="cep" class="form-control" value="{{ old('cep', $author->cep) }}" placeholder="Digite o CEP">
            </div>

            <div class="form-group">
                <label for="street_address">Endereço</label>
                <input type="text" name="street_address" id="street_address" class="form-control" value="{{ old('street_address', $author->street_address) }}">
            </div>

            <div class="form-group">
                <label for="city">Cidade</label>
                <input type="text" name="city" id="city" class="form-control" value="{{ old('city', $author->city) }}">
            </div>

            <div class="form-group">
                <label for="state">Estado</label>
                <input type="text" name="state" id="state" class="form-control" value="{{ old('state', $author->state) }}">
            </div>

            <div class="form-group">
                <label for="country">País</label>
                <input type="text" name="country" class="form-control" value="{{ old('country', $author->country) ?? 'Brasil' }}" readonly>
            </div>

            <div class="form-group">
                <label for="picture_url">Foto</label>
                <input type="file" name="picture_url" class="form-control">
                @if ($author->picture_url)
                    <img src="{{ asset('storage/' . $author->picture_url) }}" alt="{{ $author->name }}" class="img-fluid mt-2">
                @endif
            </div>

            <button type="submit" class="btn btn-primary">Salvar Alterações</button>
        </form>
    </div>
@endsection

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <!-- Inclua jQuery caso ainda não esteja incluído -->
<script>
    $(document).ready(function() {
        $('#cep').on('input', function() {
            let cep = $(this).val().replace(/\D/g, ''); // Remove qualquer caractere não numérico

            if (cep.length === 8) {
                $.ajax({
                    url: `https://brasilapi.com.br/api/cep/v2/${cep}`,
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        $('#street_address').val(data.street || ''); // Preenche o endereço
                        $('#city').val(data.city || ''); // Preenche a cidade
                        $('#state').val(data.state || ''); // Preenche o estado
                        $('#country').val('Brasil'); // Define o país como Brasil
                    },
                    error: function(xhr) {
                        alert('CEP não encontrado ou inválido.');
                        $('#street_address').val(''); // Limpa o campo de endereço
                        $('#city').val(''); // Limpa o campo de cidade
                        $('#state').val(''); // Limpa o campo de estado
                        $('#country').val('Brasil'); // Define o país como Brasil
                    }
                });
            }
        });
    });
</script>

