<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('layout');
});


Route::prefix('clientes')->group(function () {
    Route::post('/obtemClienteCpf', 'App\Http\Controllers\ClienteController@getByCpf');
    Route::post('/buscaPedidos', 'App\Http\Controllers\ClienteController@getPedidosByTipoDado');
    Route::post('/atualizar', 'App\Http\Controllers\ClienteController@criaOuAtualizaByCpf');
});

Route::prefix('pedidos')->group(function () {
    Route::get('/', 'App\Http\Controllers\PedidoController@index');
    Route::post('/', 'App\Http\Controllers\PedidoController@store');
    Route::get('/porCliente', function () { return view('pages.pedidos_clientes'); });
    Route::get('/novo', 'App\Http\Controllers\PedidoController@novoPedido');
    Route::get('/downloadCsv/{id?}', 'App\Http\Controllers\PedidoController@exportPedidosCsv');
    Route::get('/downloadCsvCompilado/', 'App\Http\Controllers\PedidoController@exportPedidosCsvCompilado');
    Route::post('/excluirPedido', 'App\Http\Controllers\PedidoController@deleteById');
    Route::post('/atualizar', 'App\Http\Controllers\PedidoController@updateById');
});

//Auth::routes();
//Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
