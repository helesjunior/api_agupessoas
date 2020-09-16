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
     * Retorna a lista de servidores e seus estágios com data de início e fim
     *
     * @param $dtExercicio
     * @return string[]
     */
    public function retornarDadosEstagios($dtExercicio)
    {
        try {
            $sql = '
SELECT NOME,
       CARGO,
       UNIDADE_EXERCICIO,
       CPF,
       SIAPE,
       DT_INGRESSO,
       CASE
           WHEN DT_FIM_ESTAGIO IS NULL THEN DT_FIM_PREVISTO
           ELSE DT_FIM_ESTAGIO END AS DT_FIM_ESTAGIO
FROM (SELECT SER.ID_SERVIDOR,
             SER.NM_SERVIDOR                                                                                     AS NOME,
             CA.DS_CARGO_RH                                                                                      AS CARGO,
             LOT1.DS_LOTACAO                                                                                     AS UNIDADE_EXERCICIO,
             DOC.NR_DOCUMENTACAO                                                                                 AS CPF,
             DF.CD_MATRICULA_SIAPE                                                                               AS SIAPE,
             DF.DT_INGRESSO_SERVICO_PUBLICO                                                                      AS DT_INGRESSO,
             ADD_MONTHS(DT_INGRESSO_SERVICO_PUBLICO, 36)                                                         AS DT_FIM_PREVISTO,
';
            $sql .= '
ADD_MONTHS(DT_INGRESSO_SERVICO_PUBLICO, 36) +
(SELECT SUM(CAST(NVL(DT_FIM_AFASTAMENTO - DT_INICIO_AFASTAMENTO, 0) AS NUMERIC)) AS DT_FIM
              FROM AFASTAMENTO
              WHERE ID_SERVIDOR = SER.ID_SERVIDOR
            AND ID_TIPO_AFASTAMENTO IN (SELECT ID_TIPO_AFASTAMENTO
                                            FROM TIPO_AFASTAMENTO
                                            WHERE CD_TIPO_AFASTAMENTO NOT IN
' . "('0069', '3123', '1005304', '3082', '1000060', '1000063', '3014', '3114', '1002904', '1003304',
                    '3115', '31211','31212', '31213', '31214', '31215', '31216', '31217'))  ";
            $sql .= '
            AND DT_INICIO_AFASTAMENTO >= DF.DT_INGRESSO_SERVICO_PUBLICO
            AND DT_FIM_AFASTAMENTO <= ADD_MONTHS(DF.DT_INGRESSO_SERVICO_PUBLICO, 42)) AS DT_FIM_ESTAGIO
      FROM SERVIDOR SER
               JOIN AGU_RH.CARGO_EFETIVO CE ON CE.ID_SERVIDOR = SER.ID_SERVIDOR
               JOIN AGU_RH.CARGO CA ON CA.ID_CARGO = CE.ID_CARGO
               JOIN AGU_RH.DOCUMENTACAO DOC ON SER.ID_SERVIDOR = DOC.ID_SERVIDOR AND DOC.ID_TIPO_DOCUMENTACAO = 1
               LEFT JOIN AGU_RH.DADO_FUNCIONAL DF ON DF.ID_SERVIDOR = SER.ID_SERVIDOR
               LEFT JOIN AGU_RH.MOVIMENTACAO MOV ON MOV.ID_SERVIDOR = SER.ID_SERVIDOR
               LEFT JOIN AGU_RH.LOTACAO LOT1 ON LOT1.ID_LOTACAO = MOV.ID_LOTACAO_EXERCICIO
      WHERE DF.DT_INGRESSO_SERVICO_PUBLICO >= TO_DATE(' . "'{$dtExercicio}'" . ', \'DD/MM/YYYY\')
        AND MOV.DT_FINAL_MOVIMENTACAO IS NULL
        AND CA.CD_CARGO_RH IN ' . "('R410004', 'R414017', '410001', '410004', '414001', '414017')" . '
      ORDER BY DF.DT_INGRESSO_SERVICO_PUBLICO, SER.NM_SERVIDOR) consulta
';
            return DB::select($sql);
        } catch (\Exception $e) {
            return ['error', 'Ocorreu um erro no carregamento de dados, por favor tente novamente.'];
        }
    }

    /**
     * Retorna a lista de afastamentos dos servidores AU e PFN
     *
     * @param $dtExercicio
     * @return mixed
     */
    public function retornarDadosAfastamentos($dtExercicio)
    {
        try {
            $sql = '
SELECT NOME,
       CARGO,
       UNIDADE_EXERCICIO,
       CPF,
       SIAPE,
       CD_AFASTAMENTO,
       DS_AFASTAMENTO,
       DT_INICIO_AFASTAMENTO,
       DT_FIM_AFASTAMENTO,
       DT_INGRESSO,
       TOTAL_MEMBROS_AFASTADOS
FROM (
         SELECT SER.NM_SERVIDOR                AS NOME
              , CA.DS_CARGO_RH                 AS CARGO
              , LOT1.DS_LOTACAO                AS UNIDADE_EXERCICIO
              , DOC.NR_DOCUMENTACAO            AS CPF
              , DF.CD_MATRICULA_SIAPE          AS SIAPE
              , DF.DT_INGRESSO_SERVICO_PUBLICO AS DT_INGRESSO
              , TA.CD_TIPO_AFASTAMENTO         AS CD_AFASTAMENTO
              , TA.DS_TIPO_AFASTAMENTO         AS DS_AFASTAMENTO
              , A.DT_INICIO_AFASTAMENTO
              , A.DT_FIM_AFASTAMENTO
              , (SELECT COUNT(DISTINCT ID_SERVIDOR)
                 FROM AFASTAMENTO
                 WHERE A.DT_INICIO_AFASTAMENTO IS NOT NULL
                   AND DT_INICIO_AFASTAMENTO >= DF.DT_INGRESSO_SERVICO_PUBLICO
                   AND DT_FIM_AFASTAMENTO <= ADD_MONTHS(DF.DT_INGRESSO_SERVICO_PUBLICO
                     , 36)
                   AND CD_TIPO_AFASTAMENTO NOT IN
                   ' . "('0069', '3123', '1005304', '3082', '1000060', '1000063', '3014', '3114', '1002904', '1003304',
                    '3115', '31211','31212', '31213', '31214', '31215', '31216', '31217')" . '
                   AND CA.CD_CARGO_RH IN ' . "('R410004', 'R414017', '410001', '410004', '414001', '414017')" . '
                   )  AS TOTAL_MEMBROS_AFASTADOS
         FROM SERVIDOR SER
                  JOIN AGU_RH.CARGO_EFETIVO CE ON CE.ID_SERVIDOR = SER.ID_SERVIDOR
                  JOIN AGU_RH.CARGO CA ON CA.ID_CARGO = CE.ID_CARGO
                  JOIN AGU_RH.DOCUMENTACAO DOC ON SER.ID_SERVIDOR = DOC.ID_SERVIDOR AND DOC.ID_TIPO_DOCUMENTACAO = 1
                  LEFT JOIN AGU_RH.DADO_FUNCIONAL DF ON DF.ID_SERVIDOR = SER.ID_SERVIDOR
                  LEFT JOIN AGU_RH.MOVIMENTACAO MOV ON MOV.ID_SERVIDOR = SER.ID_SERVIDOR
                  LEFT JOIN AGU_RH.LOTACAO LOT1 ON LOT1.ID_LOTACAO = MOV.ID_LOTACAO_EXERCICIO
                  LEFT JOIN AGU_RH.AFASTAMENTO A ON A.ID_SERVIDOR = SER.ID_SERVIDOR
                  LEFT JOIN AGU_RH.TIPO_AFASTAMENTO TA ON TA.ID_TIPO_AFASTAMENTO = A.ID_TIPO_AFASTAMENTO
         WHERE DF.DT_INGRESSO_SERVICO_PUBLICO >= TO_DATE(' . "'{$dtExercicio}'" . ', \'DD/MM/YYYY\')
           AND MOV.DT_FINAL_MOVIMENTACAO IS NULL
           AND A.DT_INICIO_AFASTAMENTO IS NOT NULL
           AND DT_INICIO_AFASTAMENTO >= DF.DT_INGRESSO_SERVICO_PUBLICO
           AND DT_FIM_AFASTAMENTO <= ADD_MONTHS(DF.DT_INGRESSO_SERVICO_PUBLICO, 42)
           AND CD_TIPO_AFASTAMENTO NOT IN
           ' . "('0069', '3123', '1005304', '3082', '1000060', '1000063', '3014', '3114', '1002904', '1003304',
                    '3115', '31211','31212', '31213', '31214', '31215', '31216', '31217')" . '
            AND CA.CD_CARGO_RH IN ' . "('R410004', 'R414017', '410001', '410004', '414001', '414017')" . '
         ORDER BY DF.DT_INGRESSO_SERVICO_PUBLICO, SER.NM_SERVIDOR, A.DT_INICIO_AFASTAMENTO ASC) consulta
            ';
            return DB::select($sql);
        } catch (\Exception $e) {
            return ['error', 'Ocorreu um erro no carregamento de dados, por favor tente novamente.'];
        }
    }

    /**
     * Retorna Listagem contendo dados para o ConectaTCU
     *
     * @return array
     * @author Ramon Ladeia <ramon.ladeia@agu.gov.br>
     * @author Celso da silva couto junior <celso.couto@agu.gov.br>
     */

    public function retornaConectaTCU($cpf)
    {
        $result = DB::table('SERVIDOR')
            ->join('DOCUMENTACAO', 'DOCUMENTACAO.ID_SERVIDOR', '=', 'SERVIDOR.ID_SERVIDOR')
            ->leftJoin('DADO_FUNCIONAL', 'DADO_FUNCIONAL.ID_SERVIDOR', '=', 'SERVIDOR.ID_SERVIDOR')
            ->leftJoin('cargo_efetivo', 'cargo_efetivo.id_servidor', '=', 'servidor.id_servidor')
            ->leftJoin('cargo', 'cargo.id_cargo', '=', 'cargo_efetivo.id_cargo')
            ->select('SERVIDOR.NM_SERVIDOR as nome do servidor',
                'DOCUMENTACAO.NR_DOCUMENTACAO as cpf',
                DB::raw("CASE
                            WHEN cargo.cd_cargo_rh IN ('410001','410004','R410004','414001','414017','R414017')
                                THEN 'ADVOGADO DA UNIÃO'
                            WHEN cargo.CD_CARGO_RH IN ('408001','408002','R408001','R408002')
                                THEN 'PROCURADOR FEDERAL'
                            ELSE 'SERVIDOR'
                          END  AS CARREIRA"),
                DB::raw("CASE
                            WHEN  servidor.in_status_servidor ='1' THEN 'ATIVO'
                            ELSE 'INATIVO'
                          END  AS STATUS"),

                'DADO_FUNCIONAL.CD_MATRICULA_SIAPE as matricula_siape',
                'cargo.cd_cargo_rh as codigo do cargo',
                'cargo.ds_cargo_rh as nome do cargo',
                DB::raw('SYSDATE as consultado_em')
            )
            ->where('DOCUMENTACAO.NR_DOCUMENTACAO', $cpf)
            ->whereIn('CD_CARGO_RH', [410001, 410004, 'R410004', 414001, 414017, 'R414017', 408001, 408002, 'R408001', 'R408002'])
            ->first();

        return $result;

    }

    /**
     * Retorna Listagem contendo dados para o afastamento por servidor
     * @feature 13
     * @return array
     * @author Thiago Mariano Damasceno <thiago.damasceno@agu.gov.br>
     */

    public function retornaAfastamentoServidor($tpDocumento, $dtInicio)
    {

        if (!is_numeric($tpDocumento)) {
            return ['error', 'Formato inválido para tipo documento, o tipo de documento só aceita números, por favor verifique o formato e tente novamente'];
        }

        try {
            Carbon::parse($dtInicio)->format('d/m/Y');
        } catch (\Exception $e) {
            return ['error', 'Formato inválido para data início, formato aceito (d/m/Y ex: 01/01/2020), por favor verifique o formato e tente novamente'];
        }

        try {

            $sql = DB::select('
            SELECT "NOME DO SERVIDOR"
                 , CARGO
                 , CPF
                 , SEXO
                 , IDADE
                 , "CODIGO UNIDADE EXERCICIO"
                 , UF
                 , "CIDADE DA UNIDADE"
                 , "NIVEL"
                 , "REGIME JURIDICO"
                 , SIAPE
                 , "UNIDADE DE EXERCICIO"
                 , "CODIGO DO AFASTAMENTO"
                 , "DESCRICAO TIPO AFASTAMENTO"
                 , "DESCRICAO CID (TIPO DE DOENCA)"
                 , "DATA DE INICIO DO AFASTAMENTO"
                 , "DATA FINAL DO AFASTAMENTO"
                 , DT_MOV
                 , DT_INICIO_MOVIMENTACAO
                 , "SITUACAO FUNCIONAL"
                 , "ORGAO DE ORIGEM"
            
            
            from (SELECT SER.NM_SERVIDOR                                                      AS "NOME DO SERVIDOR",
                         CA.DS_CARGO_RH                                                       AS CARGO,
                         DOC.NR_DOCUMENTACAO                                                  AS CPF,
                         DF.CD_MATRICULA_SIAPE                                                AS SIAPE,
                         LOT1.DS_LOTACAO                                                      AS "UNIDADE DE EXERCICIO",
                         TA.CD_TIPO_AFASTAMENTO                                               AS "CODIGO DO AFASTAMENTO",
                         TA.DS_TIPO_AFASTAMENTO                                               AS "DESCRICAO TIPO AFASTAMENTO",
                         A.DS_CID_AFASTAMENTO                                                 AS "DESCRICAO CID (TIPO DE DOENCA)",
                         A.DT_INICIO_AFASTAMENTO                                              AS "DATA DE INICIO DO AFASTAMENTO",
                         A.DT_FIM_AFASTAMENTO                                                 AS "DATA FINAL DO AFASTAMENTO"
                          ,
                         MAX(MOV.DT_INICIO_MOVIMENTACAO) OVER (PARTITION BY A.ID_AFASTAMENTO) as DT_MOV
                          ,
                         MOV.DT_INICIO_MOVIMENTACAO,
            
                         SER.CD_SEXO                                                          AS SEXO,
                         LOT1.CD_LOTACAO                                                      AS "CODIGO UNIDADE EXERCICIO",
            
                         UF.SG_UF                                                             AS UF,
                         MU.NM_MUNICIPIO || \' - \' || UF.SG_UF                                 AS "CIDADE DA UNIDADE",
                         NI.DS_NIVEL                                                          AS "NIVEL",
                         RJ.DS_REGIME_JURIDICO                                                AS "REGIME JURIDICO",
                         TS.DS_TIPO_SERVIDOR                                                  AS "SITUACAO FUNCIONAL",
                         TRUNC(MONTHS_BETWEEN(A.DT_FIM_AFASTAMENTO, SER.DT_NASCIMENTO) / 12)  AS IDADE,
                         LT.SG_ORGAO                                                          AS "ORGAO DE ORIGEM"
            
            
                  FROM AGU_RH.SERVIDOR SER
                           LEFT JOIN AGU_RH.CESSAO CES ON CES.ID_SERVIDOR = SER.ID_SERVIDOR
                           LEFT JOIN AGU_RH.REGIME_JURIDICO RJ ON
                           RJ.ID_REGIME_JURIDICO = CES.ID_REGIME_JURIDICO_DESTINO
                           LEFT JOIN AGU_RH.TIPO_SERVIDOR TS ON SER.ID_TIPO_SERVIDOR = TS.ID_TIPO_SERVIDOR
                           JOIN AGU_RH.CARGO_EFETIVO CE ON CE.ID_SERVIDOR = SER.ID_SERVIDOR
                           JOIN AGU_RH.CARGO CA ON CA.ID_CARGO = CE.ID_CARGO
                           LEFT JOIN NIVEL NI ON CA.ID_NIVEL = NI.ID_NIVEL
                           JOIN AGU_RH.DOCUMENTACAO DOC ON SER.ID_SERVIDOR = DOC.ID_SERVIDOR AND DOC.ID_TIPO_DOCUMENTACAO = ?
                           LEFT JOIN AGU_RH.DADO_FUNCIONAL DF ON DF.ID_SERVIDOR = SER.ID_SERVIDOR
                           LEFT JOIN AGU_RH.MOVIMENTACAO MOV ON MOV.ID_SERVIDOR = SER.ID_SERVIDOR
                           LEFT JOIN AGU_RH.LOTACAO LOT1 ON LOT1.ID_LOTACAO = MOV.ID_LOTACAO_EXERCICIO
                           LEFT JOIN AGU_RH.ENDERECO EN ON
                      EN.ID_ENDERECO = LOT1.ID_ENDERECO
                           LEFT JOIN AGU_RH.MUNICIPIO MU ON MU.ID_MUNICIPIO = EN.ID_MUNICIPIO
                           LEFT JOIN AGU_RH.UF UF ON
                      MU.ID_UF = UF.ID_UF
                           LEFT JOIN (
                      SELECT U.ID_SERVIDOR,
                             O.SG_ORGAO
                      FROM (
                               SELECT MAX(ID_MOVIMENTACAO) ULTIMA, ID_SERVIDOR
                               FROM MOVIMENTACAO
                               GROUP BY ID_SERVIDOR
                           ) U
                               LEFT JOIN MOVIMENTACAO N ON N.ID_MOVIMENTACAO = U.ULTIMA
                               LEFT JOIN ORGAO O ON O.ID_ORGAO = N.ID_ORGAO_MOVIMENTACAO
                  ) LT ON
                      LT.ID_SERVIDOR = SER.ID_SERVIDOR
                           JOIN AGU_RH.AFASTAMENTO A ON A.ID_SERVIDOR = SER.ID_SERVIDOR
                           INNER JOIN AGU_RH.TIPO_AFASTAMENTO TA ON TA.ID_TIPO_AFASTAMENTO = A.ID_TIPO_AFASTAMENTO
                  WHERE DT_INICIO_AFASTAMENTO >= TO_DATE(?, \'DD/MM/YY\')
                    AND MOV.DT_INICIO_MOVIMENTACAO < DT_INICIO_AFASTAMENTO
                  ORDER BY NM_SERVIDOR ASC
                         , DT_INICIO_AFASTAMENTO ASC) s
            where s.DT_MOV = s.DT_INICIO_MOVIMENTACAO
', [$tpDocumento, $dtInicio]);

            return $sql;
        } catch (\Exception $e) {
            return ['error', 'Ocorreu um erro no carregamento de dados, por favor tente novamente.'];
        }
    }

    /**
     * Retorna Listagem contendo dados para o afastamento por unidade
     * @feature 13
     * @return array
     * @author Thiago Mariano Damasceno <thiago.damasceno@agu.gov.br>
     */

    public function retornaAfastamentoUnidade($tpDocumento, $dtInicio)
    {

        if (!is_numeric($tpDocumento)) {
            return ['error', 'Formato inválido para tipo documento, o tipo de documento só aceita números, por favor verifique o formato e tente novamente'];
        }

        try {
            Carbon::parse($dtInicio)->format('d/m/Y');
        } catch (\Exception $e) {
            return ['error', 'Formato inválido para data início, formato aceito (d/m/Y ex: 01/01/2020), por favor verifique o formato e tente novamente'];
        }

        try {

            $sql = '
            SELECT
                 "UNIDADE DE EXERCICIO"
                 ,"DESCRICAO TIPO AFASTAMENTO"
                 ,"DESCRICAO CID (TIPO DE DOENCA)"
                 ,"DATA DE INICIO DO AFASTAMENTO"
                 ,"DATA FINAL DO AFASTAMENTO"
            FROM
                (SELECT DISTINCT
                     SER.NM_SERVIDOR AS "NOME DO SERVIDOR",
                     CA.DS_CARGO_RH  AS CARGO,
                     DOC.NR_DOCUMENTACAO AS CPF,
                     DF.CD_MATRICULA_SIAPE AS SIAPE,
                     LOT1.DS_LOTACAO AS "UNIDADE DE EXERCICIO",
                     TA.CD_TIPO_AFASTAMENTO AS "CODIGO DO AFASTAMENTO",
                     TA.DS_TIPO_AFASTAMENTO AS "DESCRICAO TIPO AFASTAMENTO",
                     A.DS_CID_AFASTAMENTO AS "DESCRICAO CID (TIPO DE DOENCA)",
                     A.DT_INICIO_AFASTAMENTO AS "DATA DE INICIO DO AFASTAMENTO",
                     A.DT_FIM_AFASTAMENTO AS "DATA FINAL DO AFASTAMENTO"
                         ,MAX(MOV.DT_INICIO_MOVIMENTACAO) OVER (PARTITION BY A.ID_AFASTAMENTO) as DT_MOV
                         ,MOV.DT_INICIO_MOVIMENTACAO
                FROM
                     AGU_RH.SERVIDOR SER
                         JOIN AGU_RH.CARGO_EFETIVO CE ON CE.ID_SERVIDOR = SER.ID_SERVIDOR
                         JOIN AGU_RH.CARGO CA ON CA.ID_CARGO = CE.ID_CARGO
                         JOIN AGU_RH.DOCUMENTACAO DOC ON SER.ID_SERVIDOR = DOC.ID_SERVIDOR AND DOC.ID_TIPO_DOCUMENTACAO = ?
                         LEFT JOIN AGU_RH.DADO_FUNCIONAL DF ON DF.ID_SERVIDOR = SER.ID_SERVIDOR
                         LEFT JOIN AGU_RH.MOVIMENTACAO MOV ON MOV.ID_SERVIDOR = SER.ID_SERVIDOR
                         LEFT JOIN AGU_RH.LOTACAO LOT1 ON LOT1.ID_LOTACAO = MOV.ID_LOTACAO_EXERCICIO
                         JOIN AGU_RH.AFASTAMENTO A ON A.ID_SERVIDOR = SER.ID_SERVIDOR
                         INNER JOIN AGU_RH.TIPO_AFASTAMENTO TA ON TA.ID_TIPO_AFASTAMENTO = A.ID_TIPO_AFASTAMENTO
                WHERE
                         DT_INICIO_AFASTAMENTO >= TO_DATE(?, \'DD/MM/YY\') AND MOV.DT_INICIO_MOVIMENTACAO < DT_INICIO_AFASTAMENTO
                ORDER
                     BY NM_SERVIDOR ASC
                      , DT_INICIO_AFASTAMENTO ASC) s
            where
                    s.DT_MOV = s.DT_INICIO_MOVIMENTACAO';

            return DB::select($sql, [$tpDocumento, $dtInicio]);


        } catch (\Exception $e) {
            return ['error', 'Ocorreu um erro no carregamento de dados, por favor tente novamente.'];
        }

    }

    /**
     * Retorna Listagem contendo dados para o afastamento por unidade
     * @feature 13
     * @return array
     * @author Thiago Mariano Damasceno <thiago.damasceno@agu.gov.br>
     */
    public function retornaMovimentacao()
    {
        ini_set("memory_limit", "512M");
        try {

            DB::beginTransaction();
            $sql = DB::select("WITH cargo_eftv as (
                                            SELECT CE.ID_CARGO_EFETIVO AS id_cargo_ef,
                                                   CGO.DS_CARGO_RH     as cargo_rh
                                            FROM AGU_RH.CARGO_EFETIVO CE
                                                     INNER JOIN AGU_RH.CARGO CGO ON (CE.id_cargo = CGO.ID_CARGO)
                                                     INNER JOIN AGU_RH.PROVIMENTO P ON (P.ID_CARGO_EFETIVO = CE.id_cargo_efetivo)
                                                     INNER JOIN AGU_RH.CARGO C ON C.ID_CARGO = CE.ID_CARGO
                                                     LEFT JOIN AGU_RH.CARREIRA CA ON CA.ID_CARREIRA = C.ID_CARREIRA
                                            WHERE CE.DT_OPERACAO_EXCLUSAO IS NULL
                                              AND C.DT_OPERACAO_EXCLUSAO IS NULL
                                              AND P.DT_OPERACAO_EXCLUSAO IS NULL)
                                        SELECT DISTINCT CE1.ID_CARGO_EFETIVO,
                                               SER.NR_CPF_OPERADOR,
                                               TS.DS_TIPO_SERVIDOR,
                                               LO.DS_LOTACAO AS DESCRICAO_LOT_ORIGEM,
                                               LE.DS_LOTACAO AS DESCRICAO_LOT_EXER,
                                               TO_CHAR(CE1.DT_INGRESSO_SERVIDOR, 'DD/MM/YYYY'),
                                               CA.cargo_rh,
                                               MOV.ID_TIPO_MOVIMENTACAO,
                                               MOV.DESCRICAO_MOVIMENTACAO,
                                               MOV.ORGAO_MOVIMENTACAO,
                                               MOV.ID_LOTACAO_ORIGEM,
                                               MOV.DESCRICAO_LOT_ORIGEM,
                                               MOV.COD_LOT_ORIGEM,
                                               MOV.SIGLA_LOT_ORIGEM,
                                               MOV.IDP_ORIGEM,
                                               MOV.ID_LOTACAO_EXERCICIO,
                                               MOV.DESCRICAO_LOT_EXER,
                                               MOV.COD_LOT_EXER,
                                               MOV.SIGLA_LOT_EXER,
                                               MOV.IDP_EXER,
                                               TO_CHAR(MOV.DATA_INICIO, 'DD/MM/YYYY'),
                                               CASE
                                                   WHEN MOV.DATA_FINAL IS NULL THEN
                                                           TO_CHAR(SYSDATE, 'DD/MM/YYYY') || ' - Data atual'
                                                   ELSE
                                                       TO_CHAR(MOV.DATA_FINAL, 'DD/MM/YYYY')
                                                   END       as DATA_FINAL,
                                               MOV.NOME_SERVIDOR,
                                               MOV.DESCRICAO_MUNICIPIO_LOT_EXER,
                                               MOV.SIGLA_UF_LOT_EXER
                                        FROM AGU_RH.VW_REL_MOVIMENTACAO MOV
                                                 INNER JOIN SERVIDOR SER ON SER.ID_SERVIDOR = MOV.ID_SERVIDOR
                                                 INNER JOIN AGU_RH.CARGO_EFETIVO CE1 ON CE1.ID_SERVIDOR = MOV.ID_SERVIDOR
                                                 INNER JOIN AGU_RH.TIPO_SERVIDOR TS ON (SER.ID_TIPO_SERVIDOR = TS.ID_TIPO_SERVIDOR)
                                                 INNER JOIN AGU_RH.LOTACAO LO ON (LO.ID_LOTACAO = MOV.ID_LOTACAO_ORIGEM)
                                                 INNER JOIN AGU_RH.LOTACAO LE ON (LE.ID_LOTACAO = MOV.ID_LOTACAO_EXERCICIO)
                                                 inner join cargo_eftv CA ON (CA.id_cargo_ef = CE1.ID_CARGO_EFETIVO)");
            DB::commit();
            return $sql;
        } catch (\Exception $e) {
            return ['error', 'Ocorreu um erro no carregamento de dados, por favor tente novamente.'];
        }
    }

    /**
     * Retorna Listagem contendo dados para o 4.F - Ingresso
     * @feature 178
     * @return array
     * @author Thiago Mariano Damasceno <thiago.damasceno@agu.gov.br>
     */
    public function retornaIngresso()
    {
        ini_set("memory_limit", "512M");
        try {

            DB::beginTransaction();
            $sql = DB::select("SELECT VW_REL_DADOFUNCIONAL.DATA_INGRESSO    AS \"Data Ingresso\",
                                           VW_REL_CARGOEFETIVO.DESCRICAO_CARGO   AS \"Descricao do Cargo\",
                                           VW_REL_DADOFUNCIONAL.CODIGO_MATRICULA AS \"Matricula SIAPE\",
                                           VW_REL_DADOFUNCIONAL.NOME_SERVIDOR    AS \"Nome do Servidor\",
                                           VW_REL_DADOFUNCIONAL.DESC_TIPO_ADM    AS \"Tipo Admissao - Descr.\",
                                           VW_REL_CARGOEFETIVO.ANO_CONCURSO      AS \"Concurso - Ano\",
                                           VW_REL_DADOFUNCIONAL.VINCULO_RAIS     AS \"Rais Vinculo - Descr.\"
                                    FROM VW_REL_CARGOEFETIVO,
                                         VW_REL_DADOFUNCIONAL
                                    WHERE VW_REL_CARGOEFETIVO.ID_SERVIDOR (+) = VW_REL_DADOFUNCIONAL.ID_SERVIDOR
                                      AND VW_REL_DADOFUNCIONAL.id_rh = 1");
            DB::commit();

            return $sql;
        } catch (\Exception $e) {
            return ['error', 'Ocorreu um erro no carregamento de dados, por favor tente novamente.'];
        }
    }

    /**
     * Retorna Listagem contendo dados para o 5.F - Rescisao
     * @feature 179
     * @return array
     * @author Thiago Mariano Damasceno <thiago.damasceno@agu.gov.br>
     */
    public function retornaRescisao()
    {
        ini_set("memory_limit", "512M");
        try {

            DB::beginTransaction();
            $sql = DB::select("SELECT VW_REL_CARGOEFETIVO.DESCRICAO_CARGO   AS \"descricao_do_cargo\",
                                             VW_REL_DADOFUNCIONAL.CODIGO_MATRICULA AS \"matricula_sIAPE\",
                                             VW_REL_DADOFUNCIONAL.NOME_SERVIDOR    AS \"nome_do_servidor\",
                                             TO_CHAR(VW_REL_DADOFUNCIONAL.DATA_RESCISAO, 'DD/MM/YYYY') AS \"data_rescisao\",
                                             VW_REL_DADOFUNCIONAL.RESCICAO_RAIS    AS \"rais_rescisao_descricao\",
                                             VW_REL_CARGOEFETIVO.ANO_CONCURSO      AS \"concurso_ano\"
                                     FROM VW_REL_CARGOEFETIVO, VW_REL_DADOFUNCIONAL
                                     WHERE VW_REL_CARGOEFETIVO.ID_SERVIDOR (+) = VW_REL_DADOFUNCIONAL.ID_SERVIDOR
                                     AND (VW_REL_DADOFUNCIONAL.DATA_RESCISAO IS NOT NULL)
                                     AND VW_REL_DADOFUNCIONAL.id_rh = 1");
            DB::commit();

            return $sql;
        } catch (\Exception $e) {
            return ['error', 'Ocorreu um erro no carregamento de dados, por favor tente novamente.'];
        }
    }

    /**
     * Retorna Listagem contendo dados para o Dimensão Unidade
     * @feature 32
     * @return array
     * @author Thiago Mariano Damasceno <thiago.damasceno@agu.gov.br>
     */
    public function retornaDimensaoUnidade()
    {
        ini_set("memory_limit", "512M");
        try {

            DB::beginTransaction();
            $sql = DB::select("
                                        SELECT
                                               LOTACAO.ID_LOTACAO_PAI,
                                               PAI.SG_LOTACAO as DS_LOTACAO_PAI,
                                        
                                               LOTACAO.CD_LOTACAO,
                                               LOTACAO.SG_LOTACAO,
                                               LOTACAO.CD_SIORG,
                                        
                                               LOTACAO.DS_LOTACAO,
                                               PAI.DS_LOTACAO AS DS_LOTACAO_PAI,
                                        
                                               CASE
                                                   WHEN LOTACAO.IN_ATIVO = 0
                                                       THEN 'SIM'
                                                   ELSE 'NÃO'
                                               END  AS ATIVO,
                                               LOTACAO.CD_UORG,
                                               TO_CHAR(LOTACAO.DT_CRIACAO_LOTACAO, 'DD/MM/YYYY') AS DT_CRIACAO_LOTACAO,
                                               TO_CHAR(LOTACAO.DT_EXTINCAO_LOTACAO, 'DD/MM/YYYY') AS DT_EXTINCAO_LOTACAO,
                                               TIPO_LOTACAO.DS_TIPO_LOTACAO,
                                        
                                               LOTACAO.ID_SERVIDOR_TITULAR,
                                               AGU_RH.SERVIDOR.NM_SERVIDOR,
                                               S.ID_SERVIDOR,
                                               S.NM_SERVIDOR,
                                        
                                               TELEFONE.NR_DDD  AS DDD,
                                               TELEFONE.NR_TELEFONE AS TELEFONE,
                                        
                                               MUNICIPIO.NM_MUNICIPIO AS MUNICIPIO,
                                               ENDERECO.DS_ENDERECO AS ENDERECO,
                                               ENDERECO.NM_BAIRRO  AS BAIRRO,
                                               ENDERECO.NR_CEP  AS CEP,
                                               ENDERECO.DS_COMPLEMENTO  AS COMPLEMENTO,
                                               UF.SG_UF AS UF,
                                               LOTACAO.NM_EMAIL_LOTACAO
                                        
                                        FROM AGU_RH.LOTACAO,
                                             AGU_RH.LOTACAO LOT,
                                             AGU_RH.SERVIDOR,
                                             AGU_RH.SERVIDOR S,
                                             AGU_RH.TIPO_LOTACAO,
                                             AGU_RH.LOTACAO PAI,
                                             AGU_RH.TELEFONE,
                                             AGU_RH.ENDERECO,
                                             AGU_RH.MUNICIPIO,
                                             AGU_RH.UF
                                        
                                        WHERE LOTACAO.DT_OPERACAO_EXCLUSAO IS NULL
                                          AND LOT.ID_LOTACAO (+) = AGU_RH.LOTACAO.ID_LOTACAO_PAI
                                          AND AGU_RH.SERVIDOR.ID_SERVIDOR (+) = AGU_RH.LOTACAO.ID_SERVIDOR_TITULAR
                                          AND S.ID_SERVIDOR (+) = AGU_RH.LOTACAO.ID_SERVIDOR_SUBSTITUTO
                                          AND AGU_RH.TIPO_LOTACAO.ID_TIPO_LOTACAO (+) = AGU_RH.LOTACAO.ID_TIPO_LOTACAO
                                          AND PAI.ID_LOTACAO (+) = AGU_RH.LOTACAO.ID_LOTACAO_PAI
                                          AND TELEFONE.ID_TELEFONE (+) = AGU_RH.LOTACAO.ID_TELEFONE
                                          AND ENDERECO.ID_ENDERECO (+) = AGU_RH.LOTACAO.ID_ENDERECO
                                          AND AGU_RH.MUNICIPIO.ID_MUNICIPIO (+) = ENDERECO.ID_MUNICIPIO
                                          AND AGU_RH.UF.ID_UF (+) = ENDERECO.ID_UF_ENDERECO
                                          AND LOTACAO.ID_RH = 1
                                        ORDER BY LOTACAO.CD_LOTACAO ASC");
            DB::commit();

            return $sql;
        } catch (\Exception $e) {
            return ['error', 'Ocorreu um erro no carregamento de dados, por favor tente novamente.'];
        }
    }

    /**
     * Retorna Listagem contendo dados para o controle estrutura
     * @feature 12
     * @return array
     * @author Thiago Mariano Damasceno <thiago.damasceno@agu.gov.br>
     */
    public function retornaControleEstrutura($request)
    {
        $where = array();
        $where[] = "WHERE 1 = 1";

        if ($request->get('lotacaoInicio')) {
            $where[] = "AND AA.CD_LOTACAO >= '{$request->get('lotacaoInicio')}'";
        }

        if ($request->get('lotacaoFim')) {
            $where[] = "AND AA.CD_LOTACAO <= '{$request->get('lotacaoFim')}'";
        }

        if ($request->get('funcao')) {
            $request->offsetSet('funcao', strtoupper($request->get('funcao')));
            $where[] = "AND UPPER(FG.CD_FUNCAO_GRATIFICADA) LIKE '%{$request->get('funcao')}%'";
        }

        if ($request->get('dataBase')) {
            $where[] = "  AND AA.DT_CRIACAO_CARGO <= TO_DATE('{$request->get('dataBase')}', 'dd/MM/yyyy')";
        }

        $whereFilter = implode(" ", $where);

        try {

            $sql = DB::select("SELECT CASE
                                           WHEN AA.DS_LOTACAO IS NULL THEN
                                               AA.CD_LOTACAO
                                           ELSE
                                               AA.CD_LOTACAO || ' - ' || AA.DS_LOTACAO
                                           END                                               AS LOTACAO,
                                       CASE
                                           WHEN AA.DS_CARGO_FUNCAO IS NULL THEN
                                               AA.CD_CARGO_FUNCAO
                                           ELSE
                                               AA.CD_CARGO_FUNCAO || ' - ' ||                AA.DS_CARGO_FUNCAO
                                           END                                               AS CARGO,
                                       FG.CD_FUNCAO_GRATIFICADA                              AS FUNCAO,
                                       NVL(TO_CHAR(AA.DT_CRIACAO_CARGO, 'DD/MM/YYYY'), '-')  AS DATA_CRIACAO_CARGO,
                                       NVL(TO_CHAR(AA.DT_EXTINCAO_CARGO, 'DD/MM/YYYY'), '-') AS DATA_EXTINCAO_CARGO,
                                       CASE
                                           WHEN SV.CD_SERVIDOR IS NULL THEN
                                               'Vago'
                                           ELSE
                                               SV.NM_SERVIDOR || ' (' || SV.CD_SERVIDOR || ')'
                                           END                                               AS OCUPANTE,
                                       CASE
                                           WHEN NR.NR_DOCUMENTO_NORMA IS NULL THEN
                                               '-'
                                           ELSE
                                                   FD.DS_FORMA_DOCUMENTO ||
                                                   ' ' ||
                                                   NR.NR_DOCUMENTO_NORMA ||
                                                   ' de ' ||
                                                   TO_CHAR(NR.DT_DOCUMENTO_NORMA, 'DD/MM/YYYY') ||
                                                   '. ' ||
                                                   TP.DS_TIPO_PUBLICACAO ||
                                                   ' Nº ' ||
                                                   NR.NR_PUBLICACAO_NORMA ||
                                                   ' de ' ||
                                                   TO_CHAR(NR.DT_PUBLICACAO_NORMA, 'DD/MM/YYYY') ||
                                                   '.'
                                           END                                               AS ATO,
                                       NVL(TO_CHAR(AA.DATA_POSSE, 'DD/MM/YYYY'), '-')        AS POSSE,
                                       NVL(TO_CHAR(AA.DATA_EXERCICIO, 'DD/MM/YYYY'), '-')    AS EXERCICIO,
                                       LT.SG_ORGAO                                           AS ORIGEM
                                FROM (
                                         SELECT CF.ID_FUNCAO_GRATIFICADA,
                                                CF.ID_CARGO_FUNCAO,
                                                CF.CD_CARGO_FUNCAO,
                                                CF.DS_CARGO_FUNCAO,
                                                CF.ID_RH,
                                                LT.CD_LOTACAO,
                                                LT.DS_LOTACAO,
                                                0                                            AS SUBSTITUTO,
                                                FC.ID_SERVIDOR                               AS ID_SERVIDOR,
                                                FC.ID_NORMA_NOMEACAO                         AS ID_NORMA,
                                                FC.DT_POSSE                                  AS DATA_POSSE,
                                                FC.DT_EXERCICIO                              AS DATA_EXERCICIO,
                                                CF.DT_CRIACAO_CARGO,
                                                CF.DT_EXTINCAO_CARGO
                                         FROM CARGO_FUNCAO CF
                                                  INNER JOIN
                                              LOTACAO LT ON
                                                      LT.ID_LOTACAO = CF.ID_LOTACAO
                                                      -- AND LT.CD_LOTACAO = '100000002'
                                                      AND LT.IN_ATIVO = 1
                                                      AND LT.DT_EXTINCAO_LOTACAO IS NULL
                                                      AND LT.DT_OPERACAO_EXCLUSAO IS NULL
                                                  LEFT JOIN
                                              FUNCAO_COMISSIONADA FC ON
                                                      FC.ID_CARGO_FUNCAO = CF.ID_CARGO_FUNCAO
                                                      AND DT_EXONERACAO IS NULL
                                                      AND FC.DT_OPERACAO_EXCLUSAO IS NULL
                                         WHERE CF.DT_EXTINCAO_CARGO IS NULL
                                         UNION ALL
                                         SELECT CF.ID_FUNCAO_GRATIFICADA,
                                                CF.ID_CARGO_FUNCAO,
                                                'Substituto(a)',
                                                NULL                                        AS DS_CARGO_FUNCAO,
                                                CF.ID_RH,
                                                LT.CD_LOTACAO,
                                                LT.DS_LOTACAO,
                                                1                                           AS SUBSTITUTO,
                                                FS.ID_SERVIDOR_SUBSTITUTO                   AS ID_SERVIDOR,
                                                FS.ID_NORMA_INICIO_SUBST                    AS ID_NORMA,
                                                FS.DT_INICIO_SUBSTITUICAO                   AS DATA_POSSE,
                                                FS.DT_INICIO_SUBSTITUICAO                   AS DATA_EXERCICIO,
                                                CF.DT_CRIACAO_CARGO,
                                                CF.DT_EXTINCAO_CARGO
                                         FROM CARGO_FUNCAO CF
                                                  INNER JOIN
                                              LOTACAO LT ON
                                                      LT.ID_LOTACAO = CF.ID_LOTACAO
                                                      -- AND LT.CD_LOTACAO = '100000002'
                                                      AND LT.IN_ATIVO = 1
                                                      AND LT.DT_EXTINCAO_LOTACAO IS NULL
                                                      AND LT.DT_OPERACAO_EXCLUSAO IS NULL
                                                  INNER JOIN
                                              FUNCAO_COMISSIONADA_SUBST FS ON
                                                      FS.ID_CARGO_FUNCAO = CF.ID_CARGO_FUNCAO
                                                      AND DT_FINAL_SUBSTITUICAO IS NULL
                                                      AND FS.DT_OPERACAO_EXCLUSAO IS NULL
                                         WHERE CF.DT_EXTINCAO_CARGO IS NULL
                                     ) AA
                                         LEFT JOIN
                                     FUNCAO_GRATIFICADA FG ON
                                         FG.ID_FUNCAO_GRATIFICADA = AA.ID_FUNCAO_GRATIFICADA
                                         LEFT JOIN
                                     SERVIDOR SV ON
                                         SV.ID_SERVIDOR = AA.ID_SERVIDOR
                                         LEFT JOIN
                                     NORMA NR ON
                                             NR.ID_NORMA = AA.ID_NORMA
                                             AND NR.DT_OPERACAO_EXCLUSAO IS NULL
                                         LEFT JOIN
                                     BASE_LEGAL BL ON
                                         BL.ID_BASE_LEGAL = NR.ID_BASE_LEGAL
                                         LEFT JOIN
                                     FORMA_DOCUMENTO FD ON
                                         FD.ID_FORMA_DOCUMENTO = BL.ID_FORMA_DOCUMENTO
                                         LEFT JOIN
                                     TIPO_PUBLICACAO TP ON
                                         TP.ID_TIPO_PUBLICACAO = NR.ID_TIPO_PUBLICACAO
                                         LEFT JOIN
                                     (
                                         SELECT U.ID_SERVIDOR,
                                                O.SG_ORGAO
                                         FROM (
                                                  SELECT MAX(ID_MOVIMENTACAO) ULTIMA,
                                                         ID_SERVIDOR
                                                  FROM MOVIMENTACAO
                                                  GROUP BY ID_SERVIDOR
                                              ) U
                                                  LEFT JOIN
                                              MOVIMENTACAO N ON
                                                  N.ID_MOVIMENTACAO = U.ULTIMA
                                                  LEFT JOIN
                                              ORGAO O ON
                                                  O.ID_ORGAO = N.ID_ORGAO_MOVIMENTACAO
                                     ) LT ON
                                         LT.ID_SERVIDOR = SV.ID_SERVIDOR

                                $whereFilter

                                ORDER BY
                                    AA.CD_LOTACAO ASC,
                                    FG.CD_FUNCAO_GRATIFICADA ASC,
                                    AA.ID_CARGO_FUNCAO ASC,
                                    AA.CD_CARGO_FUNCAO ASC,
                                    AA.SUBSTITUTO ASC");

            return $sql;

        } catch (\Exception $e) {
            return ['error', 'Ocorreu um erro no carregamento de dados, por favor tente novamente.'];
        }


    }

    /**
     * Retorna listagem contendo os dados do Controle de Estrutura
     *
     * @return array
     * @author Anderson Sathler <asathler@gmail.com>
     * @author Ramon Ladeia <ramon.ladeia@agu.gov.br.com>
     */
    public function retornaDadosEstrutura()
    {
        $sql = '';
        $sql .= 'SELECT ';
        $sql .= '    CASE ';
        $sql .= '        WHEN ';
        $sql .= '            CF.DS_CARGO_FUNCAO IS NULL ';
        $sql .= '        THEN ';
        $sql .= '            CF.CD_CARGO_FUNCAO ';
        $sql .= '        ELSE ';
        $sql .= '            CF.CD_CARGO_FUNCAO                    || ';
        $sql .= "            ' - '                                 || ";
        $sql .= '            CF.DS_CARGO_FUNCAO ';
        $sql .= '    END                                           AS CARGO, ';
        $sql .= '    FG.CD_FUNCAO_GRATIFICADA                      AS FUNCAO, ';
        $sql .= '    SV.NM_SERVIDOR                                || ';
        $sql .= "    ' ('                                          || ";
        $sql .= '    SV.CD_SERVIDOR                                || ';
        $sql .= "    ')'                                           AS OCUPANTE, ";
        $sql .= '    FD.DS_FORMA_DOCUMENTO                         || ';
        $sql .= "    ' '                                           || ";
        $sql .= '    NR.NR_DOCUMENTO_NORMA                         || ';
        $sql .= "    ' de '                                        || ";
        $sql .= "    TO_CHAR(NR.DT_DOCUMENTO_NORMA, 'DD/MM/YYYY')  || ";
        $sql .= "    '. '                                          || ";
        $sql .= '    TP.DS_TIPO_PUBLICACAO                         || ';
        $sql .= "    ' Nº '                                        || ";
        $sql .= '    NR.NR_PUBLICACAO_NORMA                        || ';
        $sql .= "    ' de '                                        || ";
        $sql .= "    TO_CHAR(NR.DT_PUBLICACAO_NORMA, 'DD/MM/YYYY') || ";
        $sql .= "    '.'                                           AS ATO, ";
        $sql .= '    FC.DT_POSSE                                   AS POSSE, ';
        $sql .= '    FC.DT_EXERCICIO                               AS EXERCICIO, ';
        $sql .= '    LT.SG_ORGAO                                   AS ORIGEM ';
        $sql .= 'FROM ';
        $sql .= '    FUNCAO_COMISSIONADA FC ';
        $sql .= 'LEFT JOIN ';
        $sql .= '    SERVIDOR SV ON ';
        $sql .= '        SV.ID_SERVIDOR = FC.ID_SERVIDOR ';
        $sql .= 'LEFT JOIN ';
        $sql .= '    CARGO_FUNCAO CF ON ';
        $sql .= '        CF.ID_CARGO_FUNCAO = FC.ID_CARGO_FUNCAO  ';
        $sql .= 'LEFT JOIN ';
        $sql .= '    FUNCAO_GRATIFICADA FG ON ';
        $sql .= '        FG.ID_FUNCAO_GRATIFICADA = CF.ID_FUNCAO_GRATIFICADA ';
        $sql .= 'LEFT JOIN ';
        $sql .= '    NORMA NR ON ';
        $sql .= '        NR.ID_NORMA = FC.ID_NORMA_NOMEACAO ';
        $sql .= 'LEFT JOIN ';
        $sql .= '    BASE_LEGAL BL ON ';
        $sql .= '        BL.ID_BASE_LEGAL = NR.ID_BASE_LEGAL ';
        $sql .= 'LEFT JOIN ';
        $sql .= '    FORMA_DOCUMENTO FD ON ';
        $sql .= '        FD.ID_FORMA_DOCUMENTO = BL.ID_FORMA_DOCUMENTO ';
        $sql .= 'LEFT JOIN ';
        $sql .= '    TIPO_PUBLICACAO TP ON ';
        $sql .= '        TP.ID_TIPO_PUBLICACAO = NR.ID_TIPO_PUBLICACAO ';
        $sql .= 'LEFT JOIN ';
        $sql .= '    ( ';
        $sql .= '    SELECT ';
        $sql .= '        U.ID_SERVIDOR, ';
        $sql .= '        O.SG_ORGAO ';
        $sql .= '    FROM ';
        $sql .= '        ( ';
        $sql .= '        SELECT ';
        $sql .= '            MAX(ID_MOVIMENTACAO) ULTIMA, ';
        $sql .= '            ID_SERVIDOR ';
        $sql .= '        FROM ';
        $sql .= '            MOVIMENTACAO ';
        $sql .= '        GROUP BY ';
        $sql .= '            ID_SERVIDOR ';
        $sql .= '        ) U ';
        $sql .= '    LEFT JOIN ';
        $sql .= '        MOVIMENTACAO N ON ';
        $sql .= '            N.ID_MOVIMENTACAO = U.ULTIMA ';
        $sql .= '    LEFT JOIN ';
        $sql .= '        ORGAO O ON ';
        $sql .= '            O.ID_ORGAO = N.ID_ORGAO_MOVIMENTACAO ';
        $sql .= '        ) LT ON ';
        $sql .= '            LT.ID_SERVIDOR = SV.ID_SERVIDOR  ';
        $sql .= 'ORDER BY ';
        $sql .= '    SV.NM_SERVIDOR ';

        return DB::select($sql);
    }

}
