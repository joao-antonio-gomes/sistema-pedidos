@extends('layout')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <h1>Cadastro de novo Pedido</h1>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <form method="post" id="novo-pedido">
                @csrf
                <div class="form-group">
                    <label for="cpf">CPF<sup>*</sup></label>
                    <input required type="text" name="cpf" id="cpf" class="form-control"
                           placeholder="Digite o número do seu CPF (somente números)">
                </div>
                <div class="form-group mt-3">
                    <label for="nome">Nome<sup>*</sup></label>
                    <input required type="text" name="nome" id="nome" class="form-control"
                           placeholder="Digite seu nome completo">
                </div>
                <div class="form-group mt-3">
                    <label for="email">Email<sup>*</sup></label>
                    <input required type="email" name="email" id="email" class="form-control"
                           placeholder="Digite seu e-mail">
                </div>
                <div class="form-group mt-3">
                    <label for="telefone">Telefone<sup>*</sup></label>
                    <input required type="number" name="telefone" id="telefone" class="form-control"
                           placeholder="Digite seu telefone (somente números)">
                </div>
                <div class="form-group mt-3">
                    <label for="numero_pedido">Número Pedido<sup>*</sup></label>
                    <input required type="number" name="numero_pedido" id="numero_pedido" class="form-control"
                           placeholder="Digite o número do seu pedido" readonly value="{{ $numeroPedido }}">
                </div>
                <div class="form-group mt-3">
                    <label for="produto">Produto<sup>*</sup></label>
                    <input required type="text" name="produto" id="produto" class="form-control"
                           placeholder="Descreva o nome do seu produto">
                </div>
                <div class="form-group mt-3">
                    <label for="valor">Valor<sup>*</sup></label>
                    <input required type="number" name="valor" id="valor" class="form-control"
                           placeholder="Descreva o valor do seu produto" step="0.01">
                </div>
                <div class="form-group mt-3">
                    <label for="data_pedido">Data do Pedido<sup>*</sup></label>
                    <input required type="date" name="data_pedido" id="data_pedido" class="form-control">
                </div>
                <div class="form-group mt-3">
                    <button type="submit" class="btn btn-primary">Cadastrar</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('footer-scripts')
    <script>
        function verifyLengthCPF() {
            const cpf = document.getElementById('cpf').value;
            if (cpf.length != 11) {
                alert('CPF inválido');
                return false;
            }
            return true;
        }

        function verifyLengthTelefone() {
            const telefone = document.getElementById('telefone');
            if (telefone.length < 11) {
                alert('Telefone inválido');
                return false;
            }
            return true;
        }

        function verifyAllInputsAreFullfiled() {
            const cpf = document.getElementById('cpf');
            const nome = document.getElementById('nome');
            const email = document.getElementById('email');
            const telefone = document.getElementById('telefone');
            const numero_pedido = document.getElementById('numero_pedido');
            const produto = document.getElementById('produto');
            const valor = document.getElementById('valor');
            const data_pedido = document.getElementById('data_pedido');
            if (cpf.value === '' || nome.value === '' || email.value === '' || telefone.value === '' || numero_pedido.value === '' || produto.value === '' || valor.value === '' || data_pedido.value === '') {
                alert('Preencha todos os campos');
                return false;
            }
            return true;
        }

        function doAllVerifications() {
            return verifyAllInputsAreFullfiled() && verifyLengthTelefone() && verifyLengthCPF();
        }

        document.getElementById('novo-pedido').addEventListener('submit', function (e) {
            e.preventDefault();
            if (doAllVerifications()) {
                fetch('/pedidos', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        _token: '{{ csrf_token() }}',
                        cpf: this.cpf.value,
                        nome: this.nome.value,
                        email: this.email.value,
                        telefone: this.telefone.value,
                        numero_pedido: this.numero_pedido.value,
                        produto: this.produto.value,
                        valor: this.valor.value,
                        data_pedido: this.data_pedido.value
                    })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 201) {
                            alert('Pedido cadastrado com sucesso');
                            window.location.href = '/';
                        } else {
                            const error = data.message;
                            const message = error !== '' ? error : 'Erro ao cadastrar pedido';
                            alert(message);
                        }
                    });
            }
        });

        document.getElementById('cpf').addEventListener('blur', function (e) {
            fetch('/clientes/obtemClienteCpf', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    _token: '{{ csrf_token() }}',
                    cpf: e.target.value
                })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 200) {
                        document.getElementById('nome').value = data.nome;
                        document.getElementById('email').value = data.email;
                        document.getElementById('telefone').value = data.telefone;
                    }
                });
        });
    </script>
@endsection
