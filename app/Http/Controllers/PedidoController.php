<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Pedido;
use Illuminate\Http\Request;

class PedidoController extends Controller
{
    public function index()
    {
        $clientes = $this->getInformacoesPedidosClientesCompilado();

        return view('pages.todos_pedidos', [
            "clientes" => $clientes
        ]);
    }

    public function novoPedido()
    {
        $ultimoPedido = Pedido::orderBy('id', 'desc')->first();
        $numeroProximoPedido = '1';
        if ($ultimoPedido) {
            $numeroProximoPedido = $ultimoPedido->numero_pedido + 1;
        }
        return view('pages.cadastro_novo', [
            'numeroPedido' => $numeroProximoPedido,
        ]);
    }

    public function store(Request $request)
    {
        $todosDadosPreenchidos = $request->nome && $request->cpf && $request->telefone && $request->numero_pedido &&
            $request->produto && $request->valor && $request->data_pedido && $request->email;
        if (!$todosDadosPreenchidos) {
            return response()->json(['message' => 'Dados incompletos', 'status' => 400]);
        }

        $cpfEhNumero = is_numeric($request->cpf);
        if (!$cpfEhNumero) {
            return response()->json(['message' => 'CPF inválido', 'status' => 400]);
        }

        $telefoneMenorQueDezDigitos = strlen($request->telefone) < 10;
        if ($telefoneMenorQueDezDigitos) {
            return response()->json(['message' => 'Telefone inválido', 'status' => 400]);
        }

        $ultimoPedido = Pedido::orderBy('id', 'desc')->first();
        $numeroProximoPedido = $ultimoPedido->numero_pedido + 1;
        if ($numeroProximoPedido != $request->numero_pedido) {
            return response()->json(['message' => 'Número do pedido incorreto', 'status' => 400]);
        }

        $cpfCliente = [
            'cpf' => $request->cpf,
        ];
        $dadosCliente = [
            'nome' => $request->nome,
            'telefone' => $request->telefone,
            'email' => $request->email,
        ];
        $cliente = Cliente::updateOrCreate($cpfCliente, $dadosCliente);

        $pedido = Pedido::create([
            'numero_pedido' => $request->numero_pedido,
            'produto' => $request->produto,
            'valor' => $request->valor,
            'data_pedido' => $request->data_pedido,
            'cliente_id' => (int)$cliente->id
        ]);
        return response()->json(['status' => 201]);
    }

    public function deleteById(Request $request)
    {
        $id = $request->pedido_id;
        $pedido = Pedido::find($id);
        if (!$pedido) {
            return response()->json(['message' => 'Pedido não encontrado', 'status' => 404]);
        }
        $pedido->delete();
        return response()->json(['status' => 200]);
    }

    public function updateById(Request $request)
    {
        $id = $request->id;
        $pedido = Pedido::find($id);
        if (!$pedido) {
            return response()->json(['message' => 'Pedido não encontrado', 'status' => 404]);
        }
        $pedido->produto = $request->produto;
        $pedido->valor = $request->valor;
        $pedido->data_pedido = $request->data_pedido;

        $pedido->save();

        return response()->json([
            'pedido' => $pedido,
            'status' => 200]);
    }

    public function getInformacoesPedidosClientesCompilado()
    {
        $clientes = Cliente::all();
        $pedidos = Pedido::all();

        $arrayValoresTodosPedidos = $pedidos->map(function ($pedido) {
            return $pedido->valor;
        });
        $totalValoresPedidos = $arrayValoresTodosPedidos->reduce(function ($carry, $item) {
            return $carry + $item;
        });
        foreach ($clientes as $cliente) {
            $cliente->numero_pedidos = $cliente->pedidos->count();
            $pedidosCliente = $cliente->pedidos;
            $valorTotal = $pedidosCliente->reduce(function ($carry, $pedido) {
                return $carry + $pedido->valor;
            }, 0);
            $cliente->valor_total = number_format($valorTotal, 2, ',', '.');
            $percentualPedidos = (($valorTotal / $totalValoresPedidos) * 100);
            //format percentualPedidos
            $cliente->percentual = number_format($percentualPedidos, 2, ',', '.');
        }

        return $clientes;
    }

    public function exportPedidosCsv($id = null)
    {
        if ($id == null) {
            $pedidos = Pedido::all();
        } else {
            $pedidos = Pedido::where('cliente_id', $id)->get();
        }
        $csv = fopen('php://temp/maxmemory:'. (5*1024*1024), 'r+');
        fputcsv($csv, ['Número do pedido', 'Produto', 'Valor', 'Data do pedido', 'Nome do cliente', 'CPF do cliente', 'Telefone do cliente', 'Email do cliente']);
        foreach ($pedidos as $pedido) {
            $cliente = $pedido->cliente;
            fputcsv($csv, [
                $pedido->numero_pedido,
                $pedido->produto,
                $pedido->valor,
                $pedido->data_pedido,
                $cliente->nome,
                $cliente->cpf,
                $cliente->telefone,
                $cliente->email,
            ]);
        }
        rewind($csv);
        $csv = stream_get_contents($csv);
        $hora = date('Y-m-d_H-i-s');
        $nomeArquivo = 'pedidos_completo_' . $hora . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=' . $nomeArquivo,
        ];
        return response($csv, 200, $headers);
    }

    public function exportPedidosCsvCompilado()
    {
        $clientes = $this->getInformacoesPedidosClientesCompilado();
        $csv = fopen('php://temp/maxmemory:'. (5*1024*1024), 'r+');
        fputcsv($csv, ['Nome do cliente', 'CPF', 'Telefone', 'E-mail',  'Número de pedidos', 'Valor total', 'Percentual de pedidos']);
        foreach ($clientes as $cliente) {
            fputcsv($csv, [
                $cliente->nome,
                $cliente->cpf,
                $cliente->telefone,
                $cliente->email,
                $cliente->numero_pedidos,
                $cliente->valor_total,
                $cliente->percentual,
            ]);
        }
        rewind($csv);
        $csv = stream_get_contents($csv);
        $hora = date('Y-m-d_H-i-s');
        $nomeArquivo = 'pedidos_compilado_' . $hora . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=' . $nomeArquivo,
        ];
        return response($csv, 200, $headers);
    }
}
