<!-- resources/views/authors/create.blade.php -->

@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Adicionar Autor</h1>
        <form action="{{ route('authors.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label for="name">Nome</label>
                <input type="text" name="name" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="cep">CEP</label>
                <input type="text" name="cep" id="cep" class="form-control" placeholder="Digite o CEP">
            </div>

            <div class="form-group">
                <label for="street_address">Endereço</label>
                <input type="text" name="street_address" id="street_address" class="form-control">
            </div>

            <div class="form-group">
                <label for="city">Cidade</label>
                <input type="text" name="city" id="city" class="form-control">
            </div>

            <div class="form-group">
                <label for="state">Estado</label>
                <input type="text" name="state" id="state" class="form-control">
            </div>

            <div class="form-group">
                <label for="country">País</label>
                <input type="text" name="country" class="form-control" value="Brasil" readonly>
            </div>

            <button type="submit" class="btn btn-primary">Salvar</button>
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
