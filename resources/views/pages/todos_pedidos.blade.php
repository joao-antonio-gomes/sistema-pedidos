@extends('layout')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3>Pedidos</h3>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped" id="pedidos-por-cliente">
                            <thead>
                            <tr>
                                <th>CPF</th>
                                <th>Nome</th>
                                <th>Telefone</th>
                                <th>Email</th>
                                <th>Número de Pedidos</th>
                                <th>Valor Total</th>
                                <th>% Valor Total</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($clientes as $cliente)
                                <tr>
                                    <td>{{$cliente->cpf}}</td>
                                    <td>{{$cliente->nome}}</td>
                                    <td>{{$cliente->telefone}}</td>
                                    <td>{{$cliente->email}}</td>
                                    <td>{{$cliente->numero_pedidos}}</td>
                                    <td>{{$cliente->valor_total}}</td>
                                    <td>{{$cliente->percentual ?? '00'}} %</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('footer-scripts')
    <script>
        $(document).ready( function () {
            $('#pedidos-por-cliente').DataTable();
        });
    </script>
@endsection
