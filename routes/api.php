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
Route::namespace('Api\v1')->prefix('v1')->group(function () {


    Route::get('servidor/cpf/{cpf}', 'ServidorController@buscaServidorPorCpf');
    Route::get('servidores', 'ServidorController@buscaTodosServidores');
    Route::get('conectatcu/cpf/{cpf}', 'PessoaController@buscaServidorTcu')->name('conectatcu');

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
    Route::get('antiguidade', 'PessoaController@listarAntiguidade')->name('antiguidade');

    //Rota do Controle de Estruturas
    Route::get('estrutura', 'PessoaController@listarEstrutura')->name('estrutura');

    Route::get('cessoes', 'PessoaController@listarCessoes');
    Route::get('provimentos', 'PessoaController@listarProvimentos');
    Route::get('requisicoes', 'PessoaController@listarRequisicoes');
    Route::get('vacancias', 'PessoaController@listarVacancias');

    //feature/12
    Route::get('controle-estrutura', 'PessoaController@buscaControleEstrutura')->name('controle-estrutura');

    //feature-13
    Route::get('afastamento-servidor/{tpDocumento}/{dtInicio}', 'PessoaController@buscaAfastamentoServidor')->name('afastamento-servidor');
    Route::get('afastamento-unidade/{tpDocumento}/{dtInicio}', 'PessoaController@buscaAfastamentoUnidade')->name('afastamento-unidade');

    //feature/14
    Route::get('movimentacao', 'PessoaController@buscaMovimentacao')->name('movimentacao');


    /*
    /{cpf}/{descricao_lot_exer}/descricao_lot_origem/{descricao_movimentacao}/{dt_inicial}/{dt_final}/{descricao_municipio_lot_exer/{sigla_uf_lot_exer}/{nome_servidor}
    --CPF (vai vir da tb servidor)  |
     descricao_lot_exer string tabela lotacao
    descricao_lot_origem string
    descricao_movimentacao string
    dt_inicial string
    dt_final string
    --dt_ingresso movimentacao novo data ingresso
    lotação exercício - município| descricao_municipio_lot_exer string
    lotação exercício - uf| sigla_uf_lot_exer string
    --cargo efetivo| descricao_lot_exer string situacao atual 00 cadastro servidor cadastro atual
    nome_servidor| nome_servidor string
    --situacao_funcional 14 cadastro de servidor|



    situacao funcional


    */

});

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
