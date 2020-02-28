<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Documentacao;
use App\Models\Servidor;
use App\Models\Mvcsservidor;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use phpDocumentor\Reflection\Types\Integer;
use function foo\func;

class ServidorController extends Controller
{
    public function buscaServidorPorCpf($cpf)
    {

        $servidor = Servidor::whereHas('documentacao', function ($query) use ($cpf) {
            $query->where('nr_documentacao', $cpf)
                ->where('id_tipo_documentacao', 1);
        })->get();

        return json_encode($servidor);
    }

    public function buscaTodosServidores()
    {

        $servidores = Mvcsservidor::all();

        return json_encode($servidores);
    }

}
