<?php

namespace App\Models;

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
     * Retorna listagem contendo os dados da Força de Trabalho
     *
     * @return array
     */
    public function retornaDadosForcaTrabalho()
    {
        $package = 'PKG_RELATORIO';
        $procedure = 'PR_REL_AREA_ATUACAO';
        $parametros['P_ID_RH'] = 1;
        $parametros['P_COD_ERRO'] = null;

        $result = $this->retornaDadosPorCursorDeProcedure($package, $procedure, $parametros);

        return $result;
    }

    /**
     * Retorna listagem contendo os dados de Funções
     *
     * @return array
     */
    public function retornaDadosFuncoes()
    {
        $package = 'PKG_RELATORIO';
        $procedure = 'PR_REL_FUNCAO';
        $parametros['P_ID_RH'] = 1;

        $result = $this->retornaDadosPorCursorDeProcedure($package, $procedure, $parametros);

        return $result;
    }

    /**
     * Retorna listagem contendo os dados da Apuração de Antiguidade
     *
     * @return array
     */
    public function retornaDadosAntiguidade()
    {
        $package = 'PKG_RELATORIO';
        $procedure = 'PR_REL_APURACAO_ANTIGUIDADE';
        $parametros['P_ID_CARGO'] = 1;
        $parametros['P_DT_DATA_BASE'] = '28/02/2020';
        $parametros['P_ID_RH'] = 1;
        $parametros['P_COD_ERRO'] = null;

        $result = $this->retornaDadosPorCursorDeProcedure($package, $procedure, $parametros);

        return $result;
    }

}
