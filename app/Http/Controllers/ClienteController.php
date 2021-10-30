<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    public function getByCpf(Request $request)
    {
        $cpf = $request->input('cpf');

        $cliente = Cliente::where('cpf', $cpf)->first();

        if ($cliente) {
            $cliente['status'] = 200;
            return response()->json($cliente);
        }

        return response()->json(['status' => 404]);
    }

    public function getPedidosByTipoDado(Request $request)
    {
        $requestAll = $request->get('tipo_dado');
        $tipoDado = $request->tipo_dado;
        $dado = $request->dado_cliente;

        $cliente = Cliente::where($tipoDado, $dado)->first();

        if ($cliente) {
            $pedidos = $cliente->pedidos()->get();
            $cliente['pedidos'] = $pedidos;
            $cliente['status'] = 200;
            return response()->json($cliente);
        }

        return response()->json(['status' => 404]);
    }

    public static function criaOuAtualizaByCpf(Request $request)
    {
        $cpf = $request->input('cpf');
        $cliente = Cliente::updateOrCreate(['cpf' => $cpf], $request->all());
        return response()->json([
            'status' => 200,
            'cliente' => $cliente
        ]);
    }
}
