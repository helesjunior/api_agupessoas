<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Pessoa;
use App\Models\Servidor;
use App\Models\TipoServidor;
use Illuminate\Http\Request;

class PessoaController extends Controller
{

    public function __construct()
    {
//        if (!$this->validaParametros()) {
//            die('{"erro":true}');
//        }
    }


    public function listarForcaTrabalho(Request $request)
    {
        $model = new Pessoa();
        return $model->retornaDadosForcaTrabalho();
    }

    public function listarFuncoes(Request $request)
    {
        $model = new Pessoa();
        return $model->retornaDadosFuncoes();
    }

    public function listarAntiguidade(Request $request)
    {
        $model = new Pessoa();
        $dataBase = $this->retornaDataBase();

        return $model->retornaDadosAntiguidade($dataBase);
    }

    // Retorna dados do Controle de Estruturas
    public function listarEstrutura()
    {
        $model = new Pessoa();
        return $model->retornaDadosEstrutura();
    }

    public function listarCessoes()
    {
        $model = new Pessoa();
        return $model->retornaDadosCessoes();
    }

    public function listarProvimentos()
    {
        $model = new Pessoa();
        return $model->retornaDadosProvimentos();
    }

    public function listarRequisicoes()
    {
        $model = new Pessoa();
        return $model->retornaDadosRequisicoes();
    }

    public function listarVacancias()
    {
        $model = new Pessoa();
        return $model->retornaDadosVacancias();
    }

    /**
     * Lista o tempo de estágio dos servidores contando seus afastamentos
     *
     * @see http://redminedti.agu.gov.br/redmine/issues/173
     * @param $dtExercicio
     * @return array
     */
    public function listarEstagios($dtExercicio)
    {
        $model = new Pessoa();
        return $model->retornarDadosEstagios($dtExercicio);
    }

    /**
     * Lista os afastamentos dos servidores
     *
     * @see http://redminedti.agu.gov.br/redmine/issues/173
     * @param $dtExercicio
     * @return array
     */
    public function listarAfastamentos($dtExercicio)
    {
        $model = new Pessoa();
        return $model->retornarDadosAfastamentos($dtExercicio);
    }

    /**
     * Lista os afastamentos dos servidores
     *
     * @see http://redminedti.agu.gov.br/redmine/issues/173
     * @param $dtExercicio
     * @return array
     */
    public function listarApuracaoAntiguidade($dtExercicio, $tipoCargo)
    {
        $model = new Pessoa();
        return $model->retornaApuracaoAntiguidade($dtExercicio, $tipoCargo);
    }

    public function buscaServidorTcu($cpf)
    {
        $modelo = new Pessoa();
         return json_encode($modelo->retornaConectaTCU($cpf));
    }

    /**
     * Valida parâmetros informados na requisição para exibição ou não dos dados
     *
     * @param string $dataBase
     * @return bool
     * @author Anderson Sathler <asathler@gmail.com>
     */
    public function validaParametros()
    {
        $validador = $this->getValidationFactory()->make(request()->all(), $this->retornaRegras());

        return !$validador->fails();
    }

    /**
     * Retorna array contendo as regras de validação das requisições
     *
     * @return array
     * @author Anderson Sathler <asathler@gmail.com>
     */
    public function retornaRegras()
    {
        // Token apresenta problemas no get se houver os caracteres [+] ou [/]
        $tokenEsperado = config('app.key', 'token_nao_informado');
        $tokenTcu = env('TOKEN_TCU', 'token tcu não encontrado');
        $regras['token'] = 'required|in:' . $tokenEsperado;

        $nomeRota = request()->route()->getName();
        if ($nomeRota == 'antiguidade') {
            $regras['database'] = 'required|date_format:Ymd';
        }

        if ($nomeRota == 'conectatcu') {
            $regras['token'] = 'required|in:' . $tokenTcu;
        }

        return $regras;
    }

    public function retornaFuncao()
    {
        return $this->retornaParametro('funcao', 0);
    }

    public function retornaDataBase()
    {
        return $this->retornaParametro('database');
    }

    public function retornaParametro($parametro, $default = '')
    {
        $params = request()->all();
        return isset($params[$parametro]) ? $params[$parametro] : $default;
    }



}
