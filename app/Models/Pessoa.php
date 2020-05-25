<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

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
     * @author Anderson Sathler <asathler@gmail.com>
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
     * @author Anderson Sathler <asathler@gmail.com>
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
     * @author Anderson Sathler <asathler@gmail.com>
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

    /**
     * Retorna Listagem contendo dados das Cessões
     *
     * @return array
     * @author Anderson Sathler <asathler@gmail.com>
     */
    public function retornaDadosCessoes()
    {

        $sql = '';
        $sql .= 'SELECT ';
        $sql .= '    VW_DF.COD_MATRICULA_SIAPE AS "Matrícula SIAPE", ';
        $sql .= '    VW_DF.NOME_SERVIDOR       AS "Nome do Servidor", ';
        $sql .= '    C.DS_CARGO_DESTINO        AS "Cargo Destino - Descrição", ';
        $sql .= '    OO.DS_ORGAO               AS "Órgão Origem - Descrição", ';
        $sql .= '    OD.DS_ORGAO               AS "Órgão Destino - Descrição", ';
        $sql .= '    CASE C.ST_ONUS ';
        $sql .= "        WHEN '0' THEN 'TOTAL' ";
        $sql .= "        WHEN '1' THEN 'PARCIAL' ";
        $sql .= "        ELSE 'SEM ONUS' ";
        $sql .= '    END                       AS Onus, ';
        $sql .= '    C.DT_INICIO_CESSAO        AS "Cessao - Data de Início", ';
        $sql .= '    C.DT_FIM_CESSAO           AS "Cessao - Data Fim" ';
        $sql .= 'FROM ';
        $sql .= '    AGU_RH.CESSAO C ';
        $sql .= 'INNER JOIN ';
        $sql .= '    AGU_RH.ORGAO OO ON ';
        $sql .= '        OO.ID_ORGAO = C.ID_ORGAO_ORIGEM ';
        $sql .= 'INNER JOIN ';
        $sql .= '    AGU_RH.ORGAO OD ON ';
        $sql .= '        OD.ID_ORGAO = C.ID_ORGAO_DESTINO ';
        $sql .= 'LEFT JOIN ';
        $sql .= '    AGU_RH.REGIME_JURIDICO RJ ON ';
        $sql .= '        RJ.ID_REGIME_JURIDICO = C.ID_REGIME_JURIDICO_DESTINO ';
        $sql .= 'INNER JOIN ';
        $sql .= '    AGU_RH.VW_APOIO_DADOFUNCIONAL VW_DF ON ';
        $sql .= '        VW_DF.ID_SERVIDOR = C.ID_SERVIDOR ';
        $sql .= 'WHERE ';
        $sql .= '    C.DT_OPERACAO_EXCLUSAO IS NULL ';
        $sql .= '    AND C.DT_FIM_CESSAO IS NULL ';

        return DB::select($sql);
    }

    /**
     * Retorna Listagem contendo dados dos Provimentos
     *
     * @return array
     * @author Anderson Sathler <asathler@gmail.com>
     */
    public function retornaDadosProvimentos()
    {

        $sql = '';
        $sql .= 'SELECT ';
        $sql .= '    P.DT_EXERCICIO_PROVIMENTO AS "Data Exercício", ';
        $sql .= '    C.DS_CARGO_RH             AS "Descrição do Cargo", ';
        $sql .= '    VW_DF.COD_MATRICULA_SIAPE AS "Matrícula SIAPE", ';
        $sql .= '    VW_DF.NOME_SERVIDOR       AS "Nome do Servidor", ';
        $sql .= '    TP.DS_TIPO_PROVIMENTO     AS "Provimento - Descrição Tipo", ';
        $sql .= '    CGO.NR_ANO_CONCURSO       AS "Ano Concurso" ';
        $sql .= 'FROM ';
        $sql .= '    AGU_RH.PROVIMENTO P ';
        $sql .= 'INNER JOIN ';
        $sql .= '    AGU_RH.TIPO_PROVIMENTO TP ON ';
        $sql .= '        TP.ID_TIPO_PROVIMENTO = P.ID_TIPO_PROVIMENTO ';
        $sql .= 'INNER JOIN ';
        $sql .= '    AGU_RH.CARGO_EFETIVO CGO ON ';
        $sql .= '        CGO.ID_CARGO_EFETIVO = P.ID_CARGO_EFETIVO ';
        $sql .= 'INNER JOIN ';
        $sql .= '    AGU_RH.CARGO C ON ';
        $sql .= '        C.ID_CARGO = CGO.ID_CARGO ';
        $sql .= 'INNER JOIN ';
        $sql .= '    AGU_RH.VW_APOIO_DADOFUNCIONAL VW_DF ON ';
        $sql .= '        VW_DF.ID_SERVIDOR = CGO.ID_SERVIDOR ';
        $sql .= 'WHERE ';
        $sql .= '    P.DT_OPERACAO_EXCLUSAO IS NULL ';
        $sql .= '    AND CGO.DT_OPERACAO_EXCLUSAO IS NULL ';
        $sql .= '    AND C.DT_OPERACAO_EXCLUSAO IS NULL ';

        return DB::select($sql);
    }

    /**
     * Retorna Listagem contendo dados das Requisições
     *
     * @return array
     * @author Anderson Sathler <asathler@gmail.com>
     */
    public function retornaDadosRequisicoes()
    {

        $sql = '';
        $sql .= 'SELECT ';
        $sql .= '    CASE SER.IN_STATUS_SERVIDOR ';
        $sql .= "        WHEN '0' THEN 'INATIVO' ";
        $sql .= "        WHEN '1' THEN 'ATIVO' ";
        $sql .= '    END                       AS "Status Servidor", ';
        $sql .= '    DF.CD_MATRICULA_SIAPE     AS "Matrícula SIAPE", ';
        $sql .= '    TDOC.DS_TIPO_DOCUMENTACAO AS "Tipo Doc. - Descr", ';
        $sql .= '    DOC.NR_DOCUMENTACAO       AS "Nº Documento", ';
        $sql .= '    REQ.DT_INICIO_REQUISICAO  AS "Data Início Requisição", ';
        $sql .= '    REQ.DT_FIM_REQUISICAO     AS "Data Fim Requisição", ';
        $sql .= '    ORGORIG.DS_ORGAO          AS "Órgão Origem - Descrição", ';
        $sql .= '    CASE REQ.ST_ONUS ';
        $sql .= "        WHEN '0' THEN 'TOTAL' ";
        $sql .= "        WHEN '1' THEN 'PARCIAL' ";
        $sql .= "        ELSE 'SEM ONUS' ";
        $sql .= '    END                       AS "ONUS", ';
        $sql .= '    RJ.DS_REGIME_JURIDICO     AS "Regime Jurídico - Descrição" ';
        $sql .= 'FROM ';
        $sql .= '    AGU_RH.REQUISICAO REQ ';
        $sql .= 'INNER JOIN ';
        $sql .= '    AGU_RH.REGIME_JURIDICO RJ ON ';
        $sql .= '        RJ.ID_REGIME_JURIDICO = REQ.ID_REGIME_JURIDICO ';
        $sql .= 'INNER JOIN ';
        $sql .= '    AGU_RH.CARGO ON ';
        $sql .= '        CARGO.ID_CARGO = REQ.ID_CARGO ';
        $sql .= 'LEFT JOIN ';
        $sql .= '    AGU_RH.TIPO_PADRAO TP ON ';
        $sql .= '        TP.ID_TIPO_PADRAO = REQ.ID_TIPO_PADRAO ';
        $sql .= 'INNER JOIN ';
        $sql .= '    AGU_RH.ORGAO ORGDEST ON ';
        $sql .= '        ORGDEST.ID_ORGAO = REQ.ID_ORGAO_DESTINO ';
        $sql .= 'INNER JOIN ';
        $sql .= '    AGU_RH.ORGAO ORGORIG ON ';
        $sql .= '        ORGORIG.ID_ORGAO = REQ.ID_ORGAO_ORIGEM ';
        $sql .= 'JOIN ';
        $sql .= '    AGU_RH.SERVIDOR SER ON ';
        $sql .= '        SER.ID_SERVIDOR = REQ.ID_SERVIDOR ';
        $sql .= 'LEFT JOIN ';
        $sql .= '    AGU_RH.DADO_FUNCIONAL DF ON ';
        $sql .= '        DF.ID_SERVIDOR = SER.ID_SERVIDOR ';
        $sql .= 'JOIN ';
        $sql .= '    AGU_RH.DOCUMENTACAO DOC ON ';
        $sql .= '        DOC.ID_SERVIDOR = SER.ID_SERVIDOR ';
        $sql .= '        AND DOC.ID_TIPO_DOCUMENTACAO = 1 ';
        $sql .= '        AND DOC.DT_OPERACAO_EXCLUSAO IS NULL ';
        $sql .= 'JOIN ';
        $sql .= '    AGU_RH.TIPO_DOCUMENTACAO TDOC ON ';
        $sql .= '        TDOC.ID_TIPO_DOCUMENTACAO = DOC.ID_TIPO_DOCUMENTACAO ';
        $sql .= 'WHERE ';
        $sql .= '    REQ.DT_OPERACAO_EXCLUSAO IS NULL ';
        $sql .= '    AND CARGO.DT_OPERACAO_EXCLUSAO IS NULL ';

        return DB::select($sql);
    }

    /**
     * Retorna Listagem contendo dados das Vacâncias
     *
     * @return array
     * @author Anderson Sathler <asathler@gmail.com>
     */
    public function retornaDadosVacancias()
    {

        $sql = '';
        $sql .= 'SELECT ';
        $sql .= '    C.DS_CARGO_RH             AS "Descricao do Cargo", ';
        $sql .= '    VW_DF.COD_MATRICULA_SIAPE AS "Matrícula SIAPE", ';
        $sql .= '    VW_DF.NOME_SERVIDOR       AS "Nome do Servidor", ';
        $sql .= '    V.DT_VACANCIA             AS "Vacancia - Data", ';
        $sql .= '    TV.DS_TIPO_VACANCIA       AS "Vacancia - Tipo", ';
        $sql .= '    CGO.NR_ANO_CONCURSO       AS "Ano Concurso" ';
        $sql .= 'FROM ';
        $sql .= '    AGU_RH.VACANCIA V ';
        $sql .= 'INNER JOIN ';
        $sql .= '    AGU_RH.PROVIMENTO P ON ';
        $sql .= '        P.ID_PROVIMENTO = V.ID_PROVIMENTO ';
        $sql .= 'INNER JOIN ';
        $sql .= '    AGU_RH.CARGO_EFETIVO CGO ON ';
        $sql .= '        CGO.ID_CARGO_EFETIVO = P.ID_CARGO_EFETIVO ';
        $sql .= 'INNER JOIN ';
        $sql .= '    AGU_RH.CARGO C ON ';
        $sql .= '        C.ID_CARGO = CGO.ID_CARGO ';
        $sql .= 'INNER JOIN ';
        $sql .= '    AGU_RH.TIPO_VACANCIA TV ON ';
        $sql .= '        TV.ID_TIPO_VACANCIA = V.ID_TIPO_VACANCIA ';
        $sql .= 'INNER JOIN ';
        $sql .= '    AGU_RH.VW_APOIO_DADOFUNCIONAL VW_DF ON ';
        $sql .= '        VW_DF.ID_SERVIDOR = CGO.ID_SERVIDOR ';
        $sql .= 'WHERE ';
        $sql .= '    V.DT_OPERACAO_EXCLUSAO IS NULL ';
        $sql .= '    AND P.DT_OPERACAO_EXCLUSAO IS NULL ';
        $sql .= '    AND CGO.DT_OPERACAO_EXCLUSAO IS NULL ';
        $sql .= '    AND C.DT_OPERACAO_EXCLUSAO IS NULL ';
        $sql .= '    AND DT_VACANCIA IS NOT NULL ';

        return DB::select($sql);
    }

    /**
     * Retorna Listagem contendo dados para o ConsctaTCU
     *
     * @return array
     * @author Ramon Ladeia <ramon.ladeia@agu.gov.br>
     */
    public function retornaConectaTCU($cpf)
    {

        $sql = '';
        $sql .= 'SELECT ';
        $sql .= '    SER.NM_SERVIDOR        AS "NOME DO SERVIDOR", ';
        $sql .= '    DOC.NR_DOCUMENTACAO    AS CPF, ';
        $sql .= '    DFU.CD_MATRICULA_SIAPE AS MATRICULA_SIAPE, ';
        $sql .= '    CASE ';
        $sql .= "        WHEN CAR.CD_CARGO_RH IN ('410001', '410004', 'R410004' , '414001', '414017', 'R414017') ";
        $sql .= "        THEN 'ADVOGADO DA UNIÃO' ";
        $sql .= "        WHEN CAR.CD_CARGO_RH IN ('408001', '408002', 'R408001', 'R408002') ";
        $sql .= "        THEN 'PROCURADOR FEDERAL' ";
        $sql .= "        ELSE 'SERVIDOR' ";
        $sql .= '    END                    AS CARREIRA, ';
        $sql .= '    CAR.CD_CARGO_RH        AS "CÓDIGO DO CARGO", ';
        $sql .= '    CAR.DS_CARGO_RH        AS "CARGO DO SERVIDOR", ';
        $sql .= '    CASE SER.IN_STATUS_SERVIDOR ';
        $sql .= "        WHEN '1' THEN 'ATIVO' ";
        $sql .= "        ELSE 'INATIVO' ";
        $sql .= '    END                    AS STATUS, ';
        $sql .= '    SYSDATE                AS "CONSULTADO EM" ';
        $sql .= 'FROM ';
        $sql .= '    SERVIDOR SER ';
        $sql .= 'INNER JOIN ';
        $sql .= '    AGU_RH.DOCUMENTACAO DOC ON ';
        $sql .= '        DOC.ID_SERVIDOR = SER.ID_SERVIDOR ';
        $sql .= '        AND DOC.ID_TIPO_DOCUMENTACAO = 1 ';
        $sql .= '        AND DOC.DT_OPERACAO_EXCLUSAO IS NULL ';
        $sql .= 'LEFT JOIN ';
        $sql .= '    AGU_RH.DADO_FUNCIONAL DFU ON ';
        $sql .= '        DFU.ID_SERVIDOR = SER.ID_SERVIDOR ';
        $sql .= 'LEFT JOIN ';
        $sql .= '    AGU_RH.CARGO_EFETIVO CEF ON ';
        $sql .= '        CEF.ID_SERVIDOR = SER.ID_SERVIDOR ';
        $sql .= '        AND CEF.DT_OPERACAO_EXCLUSAO IS NULL ';
        $sql .= 'LEFT JOIN ';
        $sql .= '    AGU_RH.CARGO CAR ON ';
        $sql .= '        CAR.ID_CARGO = CEF.ID_CARGO ';
        $sql .= 'WHERE ';
        $sql .= '    DOC.NR_DOCUMENTACAO = :cpf ';
        $sql .= '    AND CAR.CD_CARGO_RH IN (';
        $sql .= "        '410001', ";
        $sql .= "        '410004', ";
        $sql .= "        'R410004',  ";
        $sql .= "        '414001', ";
        $sql .= "        '414017', ";
        $sql .= "        'R414017', ";
        $sql .= "        '408001', ";
        $sql .= "        '408002', ";
        $sql .= "        'R408001', ";
        $sql .= "        'R408002' ";
        $sql .= '    ) ';
        $sql .= 'GROUP BY ';
        $sql .= '    SER.NM_SERVIDOR, ';
        $sql .= '    DOC.NR_DOCUMENTACAO, ';
        $sql .= '    DFU.CD_MATRICULA_SIAPE, ';
        $sql .= '    CAR.CD_CARGO_RH, ';
        $sql .= '    CAR.DS_CARGO_RH, ';
        $sql .= '    SER.IN_STATUS_SERVIDOR ';
        $sql .= 'ORDER BY ';
        $sql .= '    SER.NM_SERVIDOR ASC ';

        return DB::select($sql, ['cpf' => $cpf]);
    }

    /**
     * Retorna listagem contendo os dados do Controle de Estrutura
     *
     * @param int $funcao
     * @param string $dataBase
     * @return array
     * @author Ramon Ladeia <ramon.ladeia@agu.gov.br.com>
     */
    public function retornaDadosEstrutura($funcao = 0, $dataBase = '')
    {
        $dataFormatada = Carbon::createFromFormat('Ymd H:i:s', $dataBase . ' 00:00:00');
        $data = $dataFormatada->format('d/m/Y');

        $sql = '';
        $sql .= 'SELECT ';
        $sql .= '    * ';
        $sql .= 'FROM ';
        $sql .= '    AGU_RH.VW_REL_FUNCAO_COMISSIONADA ';
        $sql .= 'WHERE ';
        $sql .= '    ID_RH = 1 ';
        $sql .= '    AND ID_CARGO_FUNCAO = :cargo ';
        $sql .= "    AND DATA_EXERCICIO = 1 <= TO_DATE(:dataExercicio, 'DD/MM/YYYY') ";
        $sql .= "    AND NVL(DATA_EXONERACAO, SYSDATE) >= TO_DATE(:dataExoneracao, 'DD/MM/YYYY') ";

        /*
        $data = Carbon::createFromFormat('Ymd H:i:s', $dataBase . ' 00:00:00');

        $procedure = 'PR_REL_CONTROLE_ESTRUTURA';
        $parametros['P_ID_CARGO_FUNCAO'] = $funcao;
        $parametros['P_DT_DATA_BASE'] = $data->format('d/m/Y');
        $parametros['P_ID_RH'] = 1;
        $parametros['P_COD_ERRO'] = '';

        return $this->retornaDadosPorCursorDeProcedure($this->package, $procedure, $parametros);
        */

        return DB::select($sql, [
            'cargo' => $funcao,
            'dataExercicio' => $data,
            'dataExoneracao' => $data
        ]);
    }

}
