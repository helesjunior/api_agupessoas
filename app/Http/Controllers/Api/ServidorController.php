<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Documentacao;
use App\Models\Servidor;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use function foo\func;

class ServidorController extends Controller
{
    public function buscaServidorPorCpf($cpf)
    {
        $servidor = Servidor::whereHas('documentacao', function ($query) use ($cpf){
            $query->where('nr_documentacao',$cpf)
            ->where('id_tipo_documentacao',1);
        })->get();

        return json_encode($servidor);
    }
}
