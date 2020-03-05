<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Pessoa;
use Illuminate\Http\Request;

class PessoaController extends Controller
{

    public function listarForcaTrabalho(Request $request)
    {
        if (!$this->validaParametros()) {
            return ['erro' => true];
        }

        $model = new Pessoa();
        return $model->retornaDadosForcaTrabalho();
    }

    public function listarFuncoes(Request $request)
    {
        if (!$this->validaParametros()) {
            return ['erro' => true];
        }

        $model = new Pessoa();
        return $model->retornaDadosFuncoes();
    }

    public function listarAntiguidade(Request $request)
    {
        if (!$this->validaParametros()) {
            return ['erro' => true];
        }

        $model = new Pessoa();
        $dataBase = $this->retornaDataBase();
        return $model->retornaDadosAntiguidade($dataBase);
    }

    public function listarCessoes()
    {
        return 'cessoes';
    }

    public function listarProvimentos()
    {
        return 'provimentos';
    }

    public function listarRequisicoes()
    {
        return 'requisicoes';
    }

    public function listarVacancias()
    {
        return 'vacancias';
    }

    /**
     * @param string $dataBase
     * @return bool
     */
    public function validaParametros()
    {
        $validador = $this->getValidationFactory()->make(request()->all(), $this->retornaRegras());

        return !$validador->fails();
    }

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
