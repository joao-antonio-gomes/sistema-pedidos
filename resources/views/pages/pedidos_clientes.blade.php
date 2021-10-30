@extends('layout')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <h1>Busca de Pedidos</h1>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label for="cpf">Buscar por</label>
                <div class="row">
                    <div class="col-md-4">
                        <select name="tipo-dado" id="tipo-dado" class="form-control">
                            <option value="cpf">CPF</option>
                            <option value="nome">Nome</option>
                            <option value="id">Id</option>
                        </select>
                    </div>
                    <div class="col-md-8">
                        <input required type="number" name="dado-cliente" id="dado-cliente" class="form-control"
                               placeholder="Digite aqui a informação pela qual quer buscar" value="09355872925">
                    </div>
                </div>
            </div>

            <div class="form-group mt-3">
                <button type="button" id="buscar" class="btn btn-primary">Buscar</button>
            </div>
        </div>
    </div>

    <div class="row mt-4" id="dados-cliente" hidden>
        <div class="col-md-12">
            <div class="card">
                <div class="card-header mt-4">
                    <h3>Dados do Cliente</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <label for="nome">Nome</label>
                            <input type="text" class="form-control" id="nome">
                        </div>
                        <div class="col-md-4">
                            <label for="cpf">CPF</label>
                            <input type="text" class="form-control" readonly id="cpf">
                        </div>
                        <div class="col-md-4">
                            <label for="telefone">Telefone</label>
                            <input type="number" class="form-control" id="telefone">
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-4">
                            <label for="email">E-mail</label>
                            <input type="email" class="form-control" id="email">
                        </div>

                        <div class="col-md-6 mt-4">
                            <button type="submit" id="atualizar-cliente" class="btn btn-primary">Atualizar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <table class="table mt-4">
        <thead>
        <tr>
            <th>Nº Pedido</th>
            <th>Produto</th>
            <th>Valor</th>
            <th>Data Pedido</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td colspan="9" class="text-center">Nenhum pedido encontrado</td>
        </tr>
        </tbody>
        <tfoot>
        <tr hidden id="total">
            <td colspan="2" class="text-right">Total:</td>
            <td class="total-pedidos"></td>
        </tr>
        </tfoot>

    </table>
@endsection

@section('footer-scripts')
    <script>
        $(document).ready(function () {
            mostrarBotoesAtualizarCancelar();
            mostrarBotoesEditarExcluir();
        });

        document.getElementById('tipo-dado').addEventListener('change', function () {
            let tipoDado = this.value;
            let dadoCliente = document.getElementById('dado-cliente');
            dadoCliente.value = '';
            if (tipoDado === 'cpf' || tipoDado === 'id') {
                dadoCliente.setAttribute('type', 'number');
            } else {
                dadoCliente.setAttribute('type', 'text');
            }
        });

        //busca dados cliente por cpf
        document.getElementById('buscar').addEventListener('click', function () {
            let tipoDado = document.getElementById('tipo-dado').value;
            let dadoCliente = document.getElementById('dado-cliente').value;
            fetch('/clientes/busca-pedidos', {
                method: 'post',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    _token: "{{ csrf_token() }}",
                    tipo_dado: tipoDado,
                    dado_cliente: dadoCliente
                })
            })
                .then(response => response.json())
                .then(cliente => {
                    mostraDadosCliente(cliente);
                    constroiTabela(cliente);
                });
        });

        function constroiTabela(cliente) {
            let tbody = document.querySelector('tbody');
            tbody.innerHTML = '';
            cliente.pedidos.forEach(pedido => {
                const pedidoId = pedido.id;
                const formataDataPedido = pedido.data_pedido.split(' ');
                let tr = document.createElement('tr');

                tr.innerHTML = `
                            <td><input style="width: 180px" type="number" id="numero-pedido-${pedidoId}" readonly class="form-control" value="${pedido.numero_pedido}"></td>
                            <td><input style="width: 180px" type="text" id="produto-${pedidoId}" readonly class="form-control" value="${pedido.produto}"></td>
                            <td><input style="width: 180px" type="number" name="valor-produto" id="valor-${pedidoId}" readonly class="form-control" value="${pedido.valor}"></td>
                            <td><input style="width: 180px" type="date" id="data-${pedidoId}" readonly class="form-control" value="${formataDataPedido[0]}"></td>
                            <td>
                                 <div class="botoes-editar-excluir">
                                    <a data-pedido-id=${pedidoId} id="editar-pedido" class="btn btn-warning">Editar</a>
                                    <a data-pedido-id=${pedidoId} id="excluir-pedido" class="btn btn-danger">Excluir</a>
                                </div>
                                <div hidden class="botoes-atualizar-cancelar">
                                    <a data-pedido-id=${pedidoId} id="atualizar-pedido" class="btn btn-primary">Atualizar</a>
                                    <a data-pedido-id=${pedidoId} id="cancelar-atualizacao" class="btn btn-secondary">Cancelar</a>
                                </div>
                            </td>
                        `;
                tbody.appendChild(tr);
            });

            //reduce cliente.pedidos.valor
            let totalPedidos = cliente.pedidos.reduce((total, pedido) => total + pedido.valor, 0);
            if (cliente.pedidos.length > 0) {

                $('#total').hide().removeAttr('hidden').slideDown(600);
                $('.total-pedidos').text(totalPedidos.toLocaleString('pt-BR', {
                    style: 'currency',
                    currency: 'BRL'
                }));
            }
        }

        function mostraDadosCliente(cliente) {
            document.getElementById('cpf').value = cliente.cpf;
            document.getElementById('nome').value = cliente.nome;
            document.getElementById('telefone').value = cliente.telefone;
            document.getElementById('email').value = cliente.email;
            $('#dados-cliente').hide().removeAttr('hidden').slideDown(600);
        }

        //excluir pedido
        document.querySelector('tbody').addEventListener('click', function (event) {
            if (event.target.id === 'excluir-pedido') {
                const confirmacao = window.confirm('Deseja excluir o pedido?');
                if (!confirmacao) {
                    return;
                }
                const linha = $(event.target.parentElement.parentElement);
                let id = event.target.dataset.pedidoId;
                fetch('/pedidos/excluir-pedido', {
                    method: 'post',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        pedido_id: id
                    })
                })
                    .then(response => response.json())
                    .then(response => {
                        if (response.status === 200) {
                            alert('Pedido excluído com sucesso!');
                            linha.fadeTo(400, 0.01, () => {
                                linha.children('td, th')
                                    .animate({padding: 0})
                                    .wrapInner('<div />')
                                    .children()
                                    .slideUp(300, () => {
                                        linha.remove();
                                    });
                            });
                        } else {
                            alert('Erro ao excluir pedido!');
                        }
                    });
            }
        });

        //remover atributo readonly e mostrar botões de confirmação
        function mostrarBotoesAtualizarCancelar() {
            document.querySelector('tbody').addEventListener('click', function (event) {
                if (event.target.id === 'editar-pedido') {
                    const linha = $(event.target.parentElement.parentElement.parentElement);
                    const input = linha.find('input');
                    Array.from(input).forEach((input, index) => {
                        if (index === 0 || index >3 ) {
                            return;
                        }
                        input.removeAttribute('readonly');
                    });

                    linha.find('.botoes-atualizar-cancelar').removeAttr('hidden').show();
                    linha.find('.botoes-editar-excluir').hide();
                }
            });
        }

        //trocar botões de atualizar cancelar para confirmação
        function mostrarBotoesEditarExcluir() {
            document.querySelector('tbody').addEventListener('click', function (event) {
                if (event.target.id === 'cancelar-atualizacao') {
                    const linha = $(event.target.parentElement.parentElement.parentElement);
                    linha.find('.botoes-atualizar-cancelar').hide();
                    linha.find('.botoes-editar-excluir').show();

                    const input = linha.find('input');
                    Array.from(input).forEach((input, index) => {
                        if (index === 0 || index >3 ) {
                            return;
                        }
                        linha.find('input').attr('readonly', true);
                    });
                }
            });
        }

        //update dados cliente
        document.querySelector('#dados-cliente').addEventListener('click', function (event) {
            if (event.target.id === 'atualizar-cliente') {
                let cpf = document.getElementById('cpf').value;
                let nome = document.getElementById('nome').value;
                let telefone = document.getElementById('telefone').value;
                let email = document.getElementById('email').value;
                fetch('/clientes/atualizar', {
                    method: 'post',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        cpf: cpf,
                        nome: nome,
                        telefone: telefone,
                        email: email
                    })
                })
                    .then(response => response.json())
                    .then(response => {
                        console.log(response);
                        if (response.status === 200) {
                            alert('Dados atualizados com sucesso!');
                        } else {
                            alert('Erro ao atualizar dados!');
                        }
                    });
            }
        });

        function atualizaValorTotal() {
            let valores = $('input[name="valor-produto"]');
            valores = Array.from(valores);
            valores = valores.map(valor => parseFloat(valor.value));
            valores = valores.reduce((total, valor) => total + valor, 0);
            console.log(valores);
            $('.total-pedidos').text(valores.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' }));
        }

        //update dados pedido
        document.querySelector('tbody').addEventListener('click', function (event) {
            if (event.target.id === 'atualizar-pedido') {
                const id = $(event.target).data('pedido-id');
                const produto = $(`#produto-${id}`).val();
                const data = $(`#data-${id}`).val();
                const valor = $(`#valor-${id}`).val();
                fetch('/pedidos/atualizar', {
                    method: 'post',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        id: id,
                        produto: produto,
                        data_pedido: data,
                        valor: valor
                    })
                })
                    .then(response => response.json())
                    .then(response => {
                        console.log(response);
                        if (response.status === 200) {
                            alert('Dados atualizados com sucesso!');

                            const linha = $(event.target.parentElement.parentElement.parentElement);
                            linha.find('.botoes-atualizar-cancelar').hide();
                            linha.find('.botoes-editar-excluir').show();

                            const input = linha.find('input');
                            Array.from(input).forEach((input, index) => {
                                if (index === 0 || index >3 ) {
                                    return;
                                }
                                linha.find('input').attr('readonly', true);
                            });

                            atualizaValorTotal();
                        } else {
                            alert('Erro ao atualizar dados!');
                        }
                    });
            }
        });

    </script>
@endsection
