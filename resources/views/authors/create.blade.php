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
            
            <div class="form-group">
                <label for="picture">Foto do Autor</label>
                <input type="file" name="picture" id="picture" class="form-control" accept="image/*">
            </div>

            <button type="submit" class="btn btn-primary">Salvar</button>
        </form>
    </div>
@endsection

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> 
<script>
    $(document).ready(function() {
        $('#cep').on('input', function() {
            let cep = $(this).val().replace(/\D/g, ''); 

            if (cep.length === 8) {
                $.ajax({
                    url: `https://brasilapi.com.br/api/cep/v2/${cep}`,
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        $('#street_address').val(data.street || ''); 
                        $('#city').val(data.city || ''); 
                        $('#state').val(data.state || ''); 
                        $('#country').val('Brasil'); 
                    },
                    error: function(xhr) {
                        alert('CEP não encontrado ou inválido.');
                        $('#street_address').val('');
                        $('#city').val('');
                        $('#state').val(''); 
                        $('#country').val('Brasil'); 
                    }
                });
            }
        });
    });
</script>
