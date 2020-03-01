<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Pessoa;
use Illuminate\Http\Request;

class PessoaController extends Controller
{

    public function listarForcaTrabalho()
    {
        $model = new Pessoa();
        return $model->retornaDadosForcaTrabalho();
    }

    public function listarFuncoes()
    {
        $model = new Pessoa();
        return $model->retornaDadosFuncoes();
    }

    public function listarAntiguidade()
    {
        $model = new Pessoa();
        return $model->retornaDadosAntiguidade();
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

}
