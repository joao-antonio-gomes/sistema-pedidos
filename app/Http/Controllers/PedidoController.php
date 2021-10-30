<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Pedido;
use Illuminate\Http\Request;

class PedidoController extends Controller
{
    public function index()
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
}
