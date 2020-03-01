<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// API Consulta Banco de Dados
Route::namespace('Api\v1')->prefix('v1')->group(function() {
    Route::get('servidor/cpf/{cpf}', 'ServidorController@buscaServidorPorCpf');
    Route::get('servidores', 'ServidorController@buscaTodosServidores');
//    Route::get('/empenho/ano/{ano}/ug/{ug}/', 'EmpenhoController@buscaEmpenhoPorAnoUg');
//    Route::get('/empenhodetalhado/{dado}', 'EmpenhodetalhadoController@buscaEmpenhodetalhadoPorNumeroEmpenho');
//    Route::get('/ordembancaria/favorecido/{dado}', 'OrdembancariaController@buscaOrdembancariaPorCnpj');
//    Route::get('/ordembancaria/ano/{ano}/ug/{ug}', 'OrdembancariaController@buscaOrdembancariaPorAnoUg');
//    Route::get('/centrocusto/mesref/{mesref}/ug/{ug}', 'CentroCustoController@buscaCentroCustoPorMesrefUg');
});

// Route::namespace('Api\v1')->prefix('v1/pessoas')->middleware('auth:api')->group(function() {
Route::namespace('Api\v1')->prefix('v1/pessoas')->group(function() {
    Route::get('forca-trabalho', 'PessoaController@listarForcaTrabalho');
    Route::get('funcoes', 'PessoaController@listarFuncoes');
    Route::get('antiguidade', 'PessoaController@listarAntiguidade');
//    Route::get('cessoes', 'PessoaController@listarCessoes');
//    Route::get('provimentos', 'PessoaController@listarProvimentos');
//    Route::get('requisicoes', 'PessoaController@listarRequisicoes');
//    Route::get('vacancias', 'PessoaController@listarVacancias');
});

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
