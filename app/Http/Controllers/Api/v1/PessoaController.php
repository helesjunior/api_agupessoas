<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Pessoa;
use Illuminate\Http\Request;

class PessoaController extends Controller
{

    public function __construct()
    {
        if (!$this->validaParametros()) {
            die('{"erro":true}');
        }
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
        $regras['token'] = 'required|in:' . $tokenEsperado;

        $nomeRota = request()->route()->getName();
        if ($nomeRota == 'antiguidade') {
            $regras['database'] = 'required|date_format:Ymd';
        }

        return $regras;
    }


    public function retornaDataBase()
    {
        $params = request()->all();
        return isset($params['database']) ? $params['database'] : '';
    }

}