<?php

namespace App\Models;

use Carbon\Carbon;

class Pessoa extends Base
{

    /**
     * @var string
     */
    protected $table = 'SERVIDOR';

    /**
     * @var string
     */
    protected $primaryKey = 'ID_SERVIDOR';

    /**
     * @var string
     */
    protected $package = 'PKG_RELATORIO';

    /**
     * Retorna listagem contendo os dados da Força de Trabalho
     *
     * @return array
     */
    public function retornaDadosForcaTrabalho()
    {
        $procedure = 'PR_REL_AREA_ATUACAO';
        $parametros['P_ID_RH'] = 1;
        $parametros['P_COD_ERRO'] = '';

        return $this->retornaDadosPorCursorDeProcedure($this->package, $procedure, $parametros);
    }

    /**
     * Retorna listagem contendo os dados de Funções
     *
     * @return array
     */
    public function retornaDadosFuncoes()
    {
        $procedure = 'PR_REL_FUNCAO';
        $parametros['P_ID_RH'] = 1;

        return $this->retornaDadosPorCursorDeProcedure($this->package, $procedure, $parametros);
    }

    /**
     * Retorna listagem contendo os dados da Apuração de Antiguidade
     *
     * @param string $dataBase
     * @return array
     */
    public function retornaDadosAntiguidade($dataBase = '')
    {
        $data = Carbon::createFromFormat('Ymd H:i:s', $dataBase . ' 00:00:00');

        $procedure = 'PR_REL_APURACAO_ANTIGUIDADE';
        $parametros['P_ID_CARGO'] = 1;
        $parametros['P_DT_DATA_BASE'] = $data->format('d/m/Y');
        $parametros['P_ID_RH'] = 1;
        $parametros['P_COD_ERRO'] = '';

        return $this->retornaDadosPorCursorDeProcedure($this->package, $procedure, $parametros);
    }

}
