<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Documentacao;
use App\Models\Servidor;
use App\Models\Mvcsservidor;
use App\Models\Pessoa;
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

    /**
     * Lista rol de responsáveis
     *
     * @see http://redminedti.agu.gov.br/redmine/issues/173
     * @return array
     */
    public function listaRolResponsaveis()
    {
        $modelo = new Pessoa();

        $responsaveis = $modelo->retornaRolResponsaveis();

        return json_encode($responsaveis);
    }

}
