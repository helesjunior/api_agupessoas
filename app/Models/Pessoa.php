<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Http\Request;
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
        ini_set("memory_limit", "512M");
        try {

            DB::beginTransaction();
            $sql = DB::select("SELECT DISTINCT TRIM(C.DS_CARGO_RH)                            AS \"DESCRICAO DO CARGO\",
                VW_DF.COD_MATRICULA_SIAPE                AS \"MATRICULA SIAPE\",
                TRIM(VW_DF.NOME_SERVIDOR)                      AS \"NOME DO SERVIDOR\",
                SER.NR_CPF_OPERADOR,
                TO_CHAR(V.DT_VACANCIA, 'DD/MM/YYYY')     AS \"VACANCIA - DATA\",
                TRIM(TV.DS_TIPO_VACANCIA)                AS \"VACANCIA - TIPO\",
                CGO.NR_ANO_CONCURSO                      AS \"ANO CONCURSO\",
                DOC.NR_DOCUMENTACAO                      AS CPF,
                TO_CHAR(SER.DT_NASCIMENTO, 'DD/MM/YYYY') AS DT_NASCIMENTO,
                TRIM(MOV.CD_LOTACAO)                     AS \"COD. LOTACAO EXERCICIO\",
                TRIM(MOV.DS_LOTACAO)                     AS \"LOTACAO EXERCICIO\",
                TRIM(LOT.DS_LOTACAO)                     AS \"DESCRICAO LOTACAO\",
                CASE SER.CD_SEXO
                    WHEN 'M' THEN 'MASCULINO'
                    ELSE 'FEMININO'
                END AS CD_SEXO,
                TO_CHAR(SER.DT_NASCIMENTO, 'DD/MM/YYYY') AS DT_NASCIMENTO
        ,LOT.CD_LOTACAO                                  AS \"CODIGO UNIDADE EXERCICIO\"
        ,UF.SG_UF                                        AS UF
        ,MU.NM_MUNICIPIO || ' - ' || UF.SG_UF            AS \"CIDADE DA UNIDADE\"
        ,NI.DS_NIVEL                                     AS \"NIVEL\"
        ,RJ.DS_REGIME_JURIDICO                           AS \"REGIME JURIDICO\"
        ,TRIM(TS.DS_TIPO_SERVIDOR)                             AS \"SITUACAO FUNCIONAL\"
        ,TRIM(LT.SG_ORGAO)                                     AS \"ORGAO DE ORIGEM\"
        ,VW.DESCRICAO_TIPO_PROVIMENTO
FROM AGU_RH.VACANCIA V
         -- LEFT JOIN PROVIMENTO PRO ON PRO.ID_PROVIMENTO = V.ID_PROVIMENTO
         -- LEFT JOIN VW_REL_PROVIMENTO VW ON VW.ID_TIPO_PROVIMENTO = PRO.ID_TIPO_PROVIMENTO
         INNER JOIN
     AGU_RH.PROVIMENTO P ON
             P.ID_PROVIMENTO = V.ID_PROVIMENTO
         INNER JOIN
     AGU_RH.CARGO_EFETIVO CGO ON
             CGO.ID_CARGO_EFETIVO = P.ID_CARGO_EFETIVO
         INNER JOIN
     AGU_RH.CARGO C ON
             C.ID_CARGO = CGO.ID_CARGO
         INNER JOIN
     AGU_RH.TIPO_VACANCIA TV ON
             TV.ID_TIPO_VACANCIA = V.ID_TIPO_VACANCIA
         INNER JOIN
     AGU_RH.VW_APOIO_DADOFUNCIONAL VW_DF ON
             VW_DF.ID_SERVIDOR = CGO.ID_SERVIDOR
         JOIN
     AGU_RH.DOCUMENTACAO DOC ON
                 DOC.ID_SERVIDOR = VW_DF.ID_SERVIDOR AND DOC.ID_TIPO_DOCUMENTACAO = 1
         INNER JOIN
     AGU_RH.SERVIDOR SER ON SER.ID_SERVIDOR = VW_DF.ID_SERVIDOR
         JOIN
     PROVIMENTO PRO ON PRO.ID_PROVIMENTO = V.ID_PROVIMENTO
         JOIN
     VW_REL_PROVIMENTO VW ON VW.ID_TIPO_PROVIMENTO = PRO.ID_TIPO_PROVIMENTO AND VW.ID_SERVIDOR = VW_DF.ID_SERVIDOR
         LEFT JOIN
     VW_REL_CARGOEFETIVO CAR ON CAR.ID_SERVIDOR = VW_DF.ID_SERVIDOR
         LEFT JOIN
     AGU_RH.CESSAO CES ON CES.ID_SERVIDOR = SER.ID_SERVIDOR
         LEFT JOIN
     AGU_RH.REGIME_JURIDICO RJ ON RJ.ID_REGIME_JURIDICO = CES.ID_REGIME_JURIDICO_DESTINO
         LEFT JOIN
     AGU_RH.TIPO_SERVIDOR TS ON SER.ID_TIPO_SERVIDOR = TS.ID_TIPO_SERVIDOR
         LEFT JOIN (SELECT U.ID_SERVIDOR,
                           U.ULTIMA,
                           U.ID_LOTACAO_EXERCICIO,
                           U.CD_LOTACAO,
                           U.DS_LOTACAO
                    FROM (
                             SELECT MAX(ID_MOVIMENTACAO) ULTIMA,
                                    ID_SERVIDOR,
                                    ID_LOTACAO_EXERCICIO,
                                    CD_LOTACAO,
                                    DS_LOTACAO
                             FROM MOVIMENTACAO MOV
                                      LEFT JOIN LOTACAO LOT ON LOT.ID_LOTACAO = MOV.ID_LOTACAO_EXERCICIO
                             GROUP BY ID_SERVIDOR, ID_LOTACAO_EXERCICIO, CD_LOTACAO, DS_LOTACAO
                         ) U
                             LEFT JOIN
                         MOVIMENTACAO N ON
                                 N.ID_MOVIMENTACAO = U.ULTIMA
                             LEFT JOIN
                         ORGAO O ON
                                 O.ID_ORGAO = N.ID_ORGAO_MOVIMENTACAO
) MOV ON MOV.ID_SERVIDOR = SER.ID_SERVIDOR
         LEFT JOIN
     AGU_RH.LOTACAO LOT ON LOT.ID_LOTACAO = MOV.ID_LOTACAO_EXERCICIO
         LEFT JOIN
     AGU_RH.ENDERECO EN ON EN.ID_ENDERECO = LOT.ID_ENDERECO
         LEFT JOIN
     AGU_RH.MUNICIPIO MU ON MU.ID_MUNICIPIO = EN.ID_MUNICIPIO
         LEFT JOIN
     AGU_RH.UF UF ON MU.ID_UF = UF.ID_UF
         LEFT JOIN
     AGU_RH.AFASTAMENTO A ON A.ID_SERVIDOR = SER.ID_SERVIDOR

         JOIN AGU_RH.CARGO_EFETIVO CE ON CE.ID_SERVIDOR = SER.ID_SERVIDOR
         JOIN AGU_RH.CARGO CA ON CA.ID_CARGO = CE.ID_CARGO
         LEFT JOIN NIVEL NI ON CA.ID_NIVEL = NI.ID_NIVEL

         JOIN (SELECT U.ID_SERVIDOR, O.SG_ORGAO
               FROM (SELECT MAX(ID_MOVIMENTACAO) ULTIMA, ID_SERVIDOR
                     FROM MOVIMENTACAO
                     GROUP BY ID_SERVIDOR
                    ) U
                        LEFT JOIN MOVIMENTACAO N ON N.ID_MOVIMENTACAO = U.ULTIMA
                        LEFT JOIN ORGAO O ON O.ID_ORGAO = N.ID_ORGAO_MOVIMENTACAO
) LT ON
        LT.ID_SERVIDOR = SER.ID_SERVIDOR

WHERE V.DT_OPERACAO_EXCLUSAO IS NULL
  AND P.DT_OPERACAO_EXCLUSAO IS NULL
  AND CGO.DT_OPERACAO_EXCLUSAO IS NULL
  AND C.DT_OPERACAO_EXCLUSAO IS NULL
  AND DT_VACANCIA IS NOT NULL
");
            DB::commit();

            return $sql;
        } catch (\Exception $e) {
            return $e->getMessage();
            return ['error', 'Ocorreu um erro no carregamento de dados, por favor tente novamente.'];
        }

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
     * Retorna a lista de afastamentos dos servidores AU e PFN
     *
     * @param $request['dataExercicio']
     * @param $request['tipoCargo']
     * @return mixed
     */
    public function retornaApuracaoAntiguidade(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'dataExercicio' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return 'Data inválida, favor verificar o formato.';
        }

        try {
            if(isset($request['dataExercicio'])) {
                Carbon::parse($request['dataExercicio']);
            }
        } catch (\Exception $e) {
            return 'Data inválida, favor verificar o formato informado.';
        }

        #se data vier vazia, coloca a data de hoje. PARA POWERBI
        if(empty($request['dataExercicio'])){
            $request['dataExercicio'] = Carbon::now()->format('d/m/Y');
        }

        if(empty($request['tipoCargo']) || is_numeric($request['tipoCargo'])){
            return 'Tipo de cargo inválido, favor verificar valor informado.';
        }

        $tipoCargo = '';
        if($request['tipoCargo'] == 'adv' || $request['tipoCargo'] == 'advogado') {
            /*Advogado da União*/
            $tipoCargo = "('410001', '410004', '414001', '414017')";
            $quantidade = 'MIN';
        } elseif ($request['tipoCargo'] ==='proc' || $request['tipoCargo'] ==='procurador') {
            /*Procurador Federal*/
            $tipoCargo = "('R408001', '408001', 'R408002', '408002')";
            $quantidade = 'MAX';
        } else {
            return 'Tipo de cargo inválido, favor verificar valor informado.';
        }

        try {
            $sql = DB::select("
SELECT SERVIDOR.DS_CARGO_RH                                     AS CARGO,
       SERVIDOR.NM_SERVIDOR                                     AS NOME,
       (CASE
            WHEN SERVIDOR.NR_CLASSIFICACAO_CONCURSO = 0 THEN NULL
            ELSE SERVIDOR.NR_CLASSIFICACAO_CONCURSO END)        AS \"Classificacao Concurso Publico\",
       (CASE
            WHEN SERVIDOR.NR_ANO_CONCURSO = 0 THEN NULL
            ELSE SERVIDOR.NR_ANO_CONCURSO END)                  AS \"Ano Concurso Publico\",
       TO_CHAR(SERVIDOR.DT_NASCIMENTO, 'DD/MM/YYYY')            AS \"Data de Nascimento\",
       ROUND((SERVIDOR_DTC.TMP_CARREIRA -
              (CASE WHEN SERVIDOR.DIAS_AFASTADO IS NOT NULL THEN SERVIDOR.DIAS_AFASTADO ELSE 0 END)) / 365,
             4)                                                 AS \"Tempo de Efetivo Exercicio\",
       SERVIDOR.CD_SERVIDOR                                     AS \"APURACAO - Cod. Servidor\",
       SERVIDOR.ID_SERVIDOR                                     AS \"APURACAO - ID Servidor\",
       TO_CHAR(SERVIDOR_DTC.DT_INGRESSO_SERVIDOR, 'DD/MM/YYYY') AS \"APURACAO - Data de Ingresso\",
       (SERVIDOR_DTC.TMP_CARREIRA - (CASE
                                         WHEN SERVIDOR.DIAS_AFASTADO IS NOT NULL THEN SERVIDOR.DIAS_AFASTADO
                                         ELSE 0 END))           AS \"APURACAO - Dias de Efet Exerc\",
       (CASE
            WHEN SERVIDOR.DIAS_AFASTADO IS NOT NULL THEN SERVIDOR.DIAS_AFASTADO
            ELSE 0 END)                                         AS  \"APURACAO - Dias Afastados\",
       SERVIDOR.ID_TIPO_PROVIMENTO                              AS \"TIPO PROVIMENTO\",
       SERVIDOR.DS_TIPO_PROVIMENTO                              AS \"DESCRICAO PROVIMENTO\"
FROM (SELECT S.NM_SERVIDOR,
             S.CD_SERVIDOR,
             S.ID_SERVIDOR,
             C.CD_CARGO_RH,
             C.DS_CARGO_RH,
             P.ID_TIPO_PROVIMENTO,
             (CASE
                  WHEN S.ID_SERVIDOR IN (7913, 194492) THEN 'TRANSPOSICAO'
                  WHEN S.ID_SERVIDOR IN (13766) THEN 'AJ - INTEGRACAO'
                  ELSE TP.DS_TIPO_PROVIMENTO END)  AS DS_TIPO_PROVIMENTO,
             (SELECT SUM((CASE
                              WHEN A.DT_FIM_AFASTAMENTO > TO_DATE('{$request['dataExercicio']}', 'DD/MM/YYYY')
                                  THEN TO_DATE('{$request['dataExercicio']}', 'DD/MM/YYYY')
                              ELSE A.DT_FIM_AFASTAMENTO END + 1) - A.DT_INICIO_AFASTAMENTO)
              FROM AGU_RH.AFASTAMENTO A
                       INNER JOIN AGU_RH.TIPO_AFASTAMENTO TA ON A.ID_TIPO_AFASTAMENTO = TA.ID_TIPO_AFASTAMENTO
              WHERE TA.CD_TIPO_AFASTAMENTO IN ('1005504', '3161', '5000', '3101', '3104', '3118', '3133', '3136', '3137', '3142') 
                AND A.DT_INICIO_AFASTAMENTO < TO_DATE('{$request['dataExercicio']}', 'DD/MM/YYYY') 
                AND A.ID_SERVIDOR = S.ID_SERVIDOR) AS DIAS_AFASTADO,
             DP.NR_CLASSIFICACAO_PNE                  NR_CLASSIFICACAO_CONCURSO, 
             CE.NR_ANO_CONCURSO,
             S.DT_NASCIMENTO
      FROM AGU_RH.SERVIDOR S
               INNER JOIN AGU_RH.DOCUMENTACAO D ON S.ID_SERVIDOR = D.ID_SERVIDOR AND D.ID_TIPO_DOCUMENTACAO = 1
               INNER JOIN AGU_RH.DADO_FUNCIONAL DF ON S.ID_SERVIDOR = DF.ID_SERVIDOR
               INNER JOIN AGU_RH.CARGO_EFETIVO CE ON S.ID_SERVIDOR = CE.ID_SERVIDOR
               INNER JOIN AGU_RH.CARGO C ON CE.ID_CARGO = C.ID_CARGO
               LEFT JOIN AGU_RH.MOVIMENTACAO M ON S.ID_SERVIDOR = M.ID_SERVIDOR AND M.DT_FINAL_MOVIMENTACAO IS NULL
               LEFT JOIN AGU_RH.PROVIMENTO P ON P.ID_CARGO_EFETIVO = CE.ID_CARGO_EFETIVO
               LEFT JOIN AGU_RH.TIPO_PROVIMENTO TP ON TP.ID_TIPO_PROVIMENTO = P.ID_TIPO_PROVIMENTO
               LEFT JOIN AGU_RH.DADO_PROMOCAO DP ON DP.ID_SERVIDOR = CE.ID_SERVIDOR
      WHERE S.IN_STATUS_SERVIDOR = 1
        AND CE.DT_OPERACAO_EXCLUSAO IS NULL
        AND P.ID_PROVIMENTO NOT IN
            (SELECT ID_PROVIMENTO
             FROM AGU_RH.VACANCIA
             WHERE ID_PROVIMENTO = P.ID_PROVIMENTO
               AND DT_OPERACAO_EXCLUSAO IS NULL)
        AND C.CD_CARGO_RH IN {$tipoCargo}) SERVIDOR,
     (SELECT S1.ID_SERVIDOR,
              {$quantidade}(CE1.DT_INGRESSO_SERVIDOR)                                             AS DT_INGRESSO_SERVIDOR,
             (TO_DATE('{$request['dataExercicio']}', 'DD/MM/YYYY') + 1 -  {$quantidade}(CE1.DT_INGRESSO_SERVIDOR)) AS TMP_CARREIRA
      FROM AGU_RH.SERVIDOR S1
               INNER JOIN AGU_RH.CARGO_EFETIVO CE1 ON S1.ID_SERVIDOR = CE1.ID_SERVIDOR
               LEFT JOIN AGU_RH.PROVIMENTO P1 ON P1.ID_CARGO_EFETIVO = CE1.ID_CARGO_EFETIVO
      WHERE S1.IN_STATUS_SERVIDOR = 1
        AND CE1.DT_OPERACAO_EXCLUSAO IS NULL
        AND P1.ID_PROVIMENTO NOT IN
            (SELECT ID_PROVIMENTO
             FROM AGU_RH.VACANCIA
             WHERE ID_PROVIMENTO = P1.ID_PROVIMENTO
               AND ID_TIPO_VACANCIA <> 16
               AND ID_TIPO_VACANCIA <> 6
               AND ID_TIPO_VACANCIA <> 19
               AND DT_OPERACAO_EXCLUSAO IS NULL)
      GROUP BY S1.ID_SERVIDOR) SERVIDOR_DTC
WHERE SERVIDOR_DTC.ID_SERVIDOR = SERVIDOR.ID_SERVIDOR
GROUP BY SERVIDOR.DS_CARGO_RH, SERVIDOR.NM_SERVIDOR,
         CASE WHEN SERVIDOR.NR_CLASSIFICACAO_CONCURSO = 0 THEN NULL ELSE SERVIDOR.NR_CLASSIFICACAO_CONCURSO END,
         CASE WHEN SERVIDOR.NR_ANO_CONCURSO = 0 THEN NULL ELSE SERVIDOR.NR_ANO_CONCURSO END,
         TO_CHAR(SERVIDOR.DT_NASCIMENTO, 'DD/MM/YYYY'), TO_CHAR(SERVIDOR.DT_NASCIMENTO, 'YYYY/MM/DD'),
         ROUND((SERVIDOR_DTC.TMP_CARREIRA -
                (CASE WHEN SERVIDOR.DIAS_AFASTADO IS NOT NULL THEN SERVIDOR.DIAS_AFASTADO ELSE 0 END)) / 365, 4),
         SERVIDOR.CD_SERVIDOR, SERVIDOR.ID_SERVIDOR, TO_CHAR(SERVIDOR_DTC.DT_INGRESSO_SERVIDOR, 'DD/MM/YYYY'),
         SERVIDOR_DTC.TMP_CARREIRA -
         (CASE WHEN SERVIDOR.DIAS_AFASTADO IS NOT NULL THEN SERVIDOR.DIAS_AFASTADO ELSE 0 END),
         CASE WHEN SERVIDOR.DIAS_AFASTADO IS NOT NULL THEN SERVIDOR.DIAS_AFASTADO ELSE 0 END,
         SERVIDOR.ID_TIPO_PROVIMENTO,
         SERVIDOR.DS_TIPO_PROVIMENTO
ORDER BY ROUND((SERVIDOR_DTC.TMP_CARREIRA - (CASE
                                                 WHEN SERVIDOR.DIAS_AFASTADO IS NOT NULL THEN SERVIDOR.DIAS_AFASTADO
                                                 ELSE 0
                                             END)) / 365, 4) DESC,
         (CASE
              WHEN SERVIDOR.NR_CLASSIFICACAO_CONCURSO = 0
                  THEN NULL
              ELSE SERVIDOR.NR_CLASSIFICACAO_CONCURSO
             END) ASC,
         (CASE
              WHEN SERVIDOR.NR_ANO_CONCURSO = 0
                  THEN NULL
              ELSE SERVIDOR.NR_ANO_CONCURSO
             END) DESC,
         TO_CHAR(SERVIDOR.DT_NASCIMENTO, 'YYYY/MM/DD') ASC", []); //$dtExercicio, $request['tipoCargo']

            return $sql;
        } catch (\Exception $e) {
            return $e->getMessage();
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


        if (!is_numeric($cpf)) {
            return ['error', 'Formato inválido para o cpf,o campo só aceita números, por favor verifique o formato e tente novamente'];
        }

        try {

            $sql = DB::select('


SELECT "SERVIDOR"."NM_SERVIDOR" as "NOME DO SERVIDOR", "DOCUMENTACAO"."NR_DOCUMENTACAO" as "CPF",
       CASE
           WHEN cargo.cd_cargo_rh IN (\'410001\',\'410004\',\'R410004\',\'414001\',\'414017\',\'R414017\')
               THEN \'ADVOGADO DA UNIÃO\'
           WHEN cargo.CD_CARGO_RH IN (\'408001\',\'408002\',\'R408001\',\'R408002\')
               THEN \'PROCURADOR FEDERAL\'
           ELSE \'SERVIDOR\'
           END  AS CARREIRA,
       CASE
           WHEN  servidor.in_status_servidor = \'1\' THEN \'ATIVO\'
           ELSE \'INATIVO\'
           END  AS STATUS, "DADO_FUNCIONAL"."CD_MATRICULA_SIAPE" as "MATRICULA_SIAPE", "CARGO"."CD_CARGO_RH" as "CODIGO DO CARGO", "CARGO"."DS_CARGO_RH" as "NOME DO CARGO", TO_CHAR(SYSDATE, \'dd/mm/yyyy\') as consultado_em
from "SERVIDOR" inner join "DOCUMENTACAO" on "DOCUMENTACAO"."ID_SERVIDOR" = "SERVIDOR"."ID_SERVIDOR"
                left join "DADO_FUNCIONAL" on "DADO_FUNCIONAL"."ID_SERVIDOR" = "SERVIDOR"."ID_SERVIDOR"
                left join "CARGO_EFETIVO" on "CARGO_EFETIVO"."ID_SERVIDOR" = "SERVIDOR"."ID_SERVIDOR"
                left join "CARGO" on "CARGO"."ID_CARGO" = "CARGO_EFETIVO"."ID_CARGO"
where "DOCUMENTACAO"."NR_DOCUMENTACAO" = ?
  and "CD_CARGO_RH" in (\'410001\',\'410004\',\'R410004\',\'414001\',\'414017\',\'R414017\',\'408001\',\'408002\',\'R408001\',\'R408002\') and rownum = 1
            ', [$cpf]);

            if($sql == null) {
                die('CPF encontrado, o código encontrado não atende ao requisito de busca.');
            }

            return $sql;

        } catch (\Exception $e) {
            return ["error", "Ocorreu um erro no carregamento de dados, por favor tente novamente."];
        }

        return $sql;
    }

    /**
     * Retorna Listagem contendo dados para o afastamento por servidor
     * @feature 13
     * @return array
     * @author Thiago Mariano Damasceno <thiago.damasceno@agu.gov.br>
     */

    public function retornaAfastamentoServidor($tpDocumento, $dtInicio)
    {

        ini_set("memory_limit", "512M");


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
            SELECT DISTINCT "NOME DO SERVIDOR"
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
            from (SELECT TRIM(SER.NM_SERVIDOR)                                                AS "NOME DO SERVIDOR",
                         TRIM(CA.DS_CARGO_RH)                                                 AS CARGO,
                         DOC.NR_DOCUMENTACAO                                                  AS CPF,
                         DF.CD_MATRICULA_SIAPE                                                AS SIAPE,
                         TRIM(LOT1.DS_LOTACAO)                                                      AS "UNIDADE DE EXERCICIO",
                         TA.CD_TIPO_AFASTAMENTO                                               AS "CODIGO DO AFASTAMENTO",
                         TRIM(TA.DS_TIPO_AFASTAMENTO)                                               AS "DESCRICAO TIPO AFASTAMENTO",
                         TRIM(A.DS_CID_AFASTAMENTO)                                                 AS "DESCRICAO CID (TIPO DE DOENCA)",
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
                         TRIM(TS.DS_TIPO_SERVIDOR)                                                  AS "SITUACAO FUNCIONAL",
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
                           JOIN (
                      SELECT U.ID_SERVIDOR,
                                        O.DS_ORGAO || \' - \' || O.SG_ORGAO AS SG_ORGAO
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

        ini_set("memory_limit", "5112M");


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
                     TRIM(LOT1.DS_LOTACAO) AS "UNIDADE DE EXERCICIO",
                     TA.CD_TIPO_AFASTAMENTO AS "CODIGO DO AFASTAMENTO",
                     TRIM(TA.DS_TIPO_AFASTAMENTO) AS "DESCRICAO TIPO AFASTAMENTO",
                     TRIM(A.DS_CID_AFASTAMENTO) AS "DESCRICAO CID (TIPO DE DOENCA)",
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
                WHERE
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
        ini_set("set_time_limit", "600");

        try {

            DB::beginTransaction();
            $sql = DB::select("WITH cargo_eftv as (
                                    SELECT DISTINCT CE.ID_CARGO_EFETIVO AS id_cargo_ef,
                                           TRIM(CGO.DS_CARGO_RH)     as cargo_rh
                                    FROM AGU_RH.CARGO_EFETIVO CE
                                             INNER JOIN AGU_RH.CARGO CGO ON (CE.id_cargo = CGO.ID_CARGO)
                                             INNER JOIN AGU_RH.PROVIMENTO P ON (P.ID_CARGO_EFETIVO = CE.id_cargo_efetivo)
                                             INNER JOIN AGU_RH.CARGO C ON C.ID_CARGO = CE.ID_CARGO
                                             LEFT JOIN AGU_RH.CARREIRA CA ON CA.ID_CARREIRA = C.ID_CARREIRA
                                    WHERE CE.DT_OPERACAO_EXCLUSAO IS NULL
                                      AND C.DT_OPERACAO_EXCLUSAO IS NULL
                                      AND P.DT_OPERACAO_EXCLUSAO IS NULL
                                    )
                                    SELECT DISTINCT CE1.ID_CARGO_EFETIVO,
                                                DOC.NR_DOCUMENTACAO                  AS CPF_SERVIDOR,
                                                SER.NR_CPF_OPERADOR,
                                                TRIM(TS.DS_TIPO_SERVIDOR),
                                                TRIM(LO.DS_LOTACAO) AS DESCRICAO_LOT_ORIGEM,
                                                TRIM(LE.DS_LOTACAO) AS DESCRICAO_LOT_EXER,
                                                TO_CHAR(CE1.DT_INGRESSO_SERVIDOR, 'DD/MM/YYYY'),
                                                TRIM(CA.cargo_rh),
                                                MOV.ID_TIPO_MOVIMENTACAO,
                                                MOV.DESCRICAO_MOVIMENTACAO,
                                                TRIM(MOV.ORGAO_MOVIMENTACAO),
                                                MOV.ID_LOTACAO_ORIGEM,
                                                TRIM(MOV.DESCRICAO_LOT_ORIGEM),
                                                MOV.COD_LOT_ORIGEM,
                                                TRIM(MOV.SIGLA_LOT_ORIGEM),
                                                MOV.IDP_ORIGEM,
                                                MOV.ID_LOTACAO_EXERCICIO,
                                                TRIM(MOV.DESCRICAO_LOT_EXER),
                                                MOV.COD_LOT_EXER,
                                                TRIM(MOV.SIGLA_LOT_EXER),
                                                MOV.IDP_EXER,
                                                TO_CHAR(MOV.DATA_INICIO, 'DD/MM/YYYY'),
                                                TO_CHAR(MOV.DATA_FINAL, 'DD/MM/YYYY'),
                                                TRIM(MOV.NOME_SERVIDOR),
                                                MOV.DESCRICAO_MUNICIPIO_LOT_EXER,
                                                MOV.SIGLA_UF_LOT_EXER
                                    FROM AGU_RH.VW_REL_MOVIMENTACAO MOV
                                         JOIN AGU_RH.DOCUMENTACAO DOC ON DOC.ID_SERVIDOR = MOV.ID_SERVIDOR AND DOC.ID_TIPO_DOCUMENTACAO = 1
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
            $sql = DB::select("WITH dados as (
                                    SELECT DISTINCT
                                                    DAD.DATA_INGRESSO                  AS \"Data Ingresso\",
                                                    TRIM(CAR.DESCRICAO_CARGO)            AS \"Descricao do Cargo\",
                                                    DAD.CODIGO_MATRICULA                 AS \"Matricula SIAPE\",
                                                    TRIM(DAD.NOME_SERVIDOR)                    AS \"Nome do Servidor\",
                                                    DOC.NR_DOCUMENTACAO                  AS CPF_SERVIDOR,
                                                    SER.DT_NASCIMENTO,
                                                    SER.ID_SERVIDOR,
                                                    DAD.DESC_TIPO_ADM                    AS \"Tipo Admissao - Descr.\",
                                                    CAR.ANO_CONCURSO                     AS \"Concurso - Ano\",
                                                    DAD.VINCULO_RAIS                     AS \"Rais Vinculo - Descr.\",
                                                    CASE SER.CD_SEXO
                                                        WHEN 'M' THEN 'Masculino'
                                                        ELSE 'Feminino'
                                                        END                              as CD_SEXO,
                                                    LOT.CD_LOTACAO                       AS \"CODIGO UNIDADE EXERCICIO\",
                                                    UF.SG_UF                             AS UF,
                                                    MU.NM_MUNICIPIO || ' - ' || UF.SG_UF AS \"CIDADE DA UNIDADE\",
                                                    NI.DS_NIVEL                          AS \"NIVEL\",
                                                    RJ.DS_REGIME_JURIDICO                AS \"REGIME JURIDICO\",
                                                    TS.DS_TIPO_SERVIDOR                  AS \"SITUACAO FUNCIONAL\",
                                                    LT.SG_ORGAO                          AS \"ORGAO DE ORIGEM\"

                                    FROM VW_REL_CARGOEFETIVO CAR
                                        JOIN AGU_RH.DOCUMENTACAO DOC ON DOC.ID_SERVIDOR = CAR.ID_SERVIDOR AND DOC.ID_TIPO_DOCUMENTACAO = 1
                                        RIGHT JOIN VW_REL_DADOFUNCIONAL DAD ON DAD.ID_SERVIDOR = CAR.ID_SERVIDOR
                                        LEFT JOIN AGU_RH.SERVIDOR SER ON SER.ID_SERVIDOR = CAR.ID_SERVIDOR
                                        LEFT JOIN AGU_RH.CESSAO CES ON CES.ID_SERVIDOR = SER.ID_SERVIDOR
                                        LEFT JOIN AGU_RH.REGIME_JURIDICO RJ ON RJ.ID_REGIME_JURIDICO = CES.ID_REGIME_JURIDICO_DESTINO
                                        LEFT JOIN AGU_RH.TIPO_SERVIDOR TS ON SER.ID_TIPO_SERVIDOR = TS.ID_TIPO_SERVIDOR
                                        LEFT JOIN (
                                        SELECT U.ID_SERVIDOR,
                                        U.ULTIMA,
                                        U.ID_LOTACAO_EXERCICIO,
                                        U.DT_INICIO_MOVIMENTACAO
                                        FROM (
                                        SELECT MAX(ID_MOVIMENTACAO) ULTIMA,
                                        ID_SERVIDOR,
                                        ID_LOTACAO_EXERCICIO,
                                        DT_INICIO_MOVIMENTACAO
                                        FROM MOVIMENTACAO
                                        GROUP BY ID_SERVIDOR, ID_LOTACAO_EXERCICIO, DT_INICIO_MOVIMENTACAO
                                        ) U
                                        LEFT JOIN
                                        MOVIMENTACAO N ON
                                        N.ID_MOVIMENTACAO = U.ULTIMA
                                        LEFT JOIN
                                        ORGAO O ON
                                        O.ID_ORGAO = N.ID_ORGAO_MOVIMENTACAO
                                        ) MOV ON MOV.ID_SERVIDOR = SER.ID_SERVIDOR
                                        LEFT JOIN AGU_RH.LOTACAO LOT ON LOT.ID_LOTACAO = MOV.ID_LOTACAO_EXERCICIO
                                        LEFT JOIN AGU_RH.ENDERECO EN ON EN.ID_ENDERECO = LOT.ID_ENDERECO
                                        LEFT JOIN AGU_RH.MUNICIPIO MU ON MU.ID_MUNICIPIO = EN.ID_MUNICIPIO
                                        LEFT JOIN AGU_RH.UF UF ON MU.ID_UF = UF.ID_UF
                                        JOIN AGU_RH.CARGO_EFETIVO CE ON CE.ID_SERVIDOR = SER.ID_SERVIDOR
                                        JOIN AGU_RH.CARGO CA ON CA.ID_CARGO = CE.ID_CARGO
                                        LEFT JOIN NIVEL NI ON CA.ID_NIVEL = NI.ID_NIVEL
                                        left JOIN AGU_RH.AFASTAMENTO A ON A.ID_SERVIDOR = SER.ID_SERVIDOR
                                        JOIN (SELECT U.ID_SERVIDOR,
                                        O.DS_ORGAO || ' - ' || O.SG_ORGAO AS SG_ORGAO
                                        FROM (
                                        SELECT MAX(ID_MOVIMENTACAO) ULTIMA, ID_SERVIDOR
                                        FROM MOVIMENTACAO
                                        GROUP BY ID_SERVIDOR
                                        ) U
                                        LEFT JOIN MOVIMENTACAO N ON N.ID_MOVIMENTACAO = U.ULTIMA
                                        LEFT JOIN ORGAO O ON O.ID_ORGAO = N.ID_ORGAO_MOVIMENTACAO
                                        ) LT ON
                                        LT.ID_SERVIDOR = SER.ID_SERVIDOR
                                    WHERE DAD.id_rh = 1
                                      AND TO_DATE(DAD.DATA_INGRESSO,'DD-MM-YYYY') = MOV.DT_INICIO_MOVIMENTACAO
                                      and CAR.ANO_CONCURSO is not null
                                ),
                                     afast as (
                                         SELECT ID_SERVIDOR,MAX(DT_FIM_AFASTAMENTO) as DT_FIM_AFASTAMENTO FROM AGU_RH.AFASTAMENTO group by ID_SERVIDOR
                                     )
                                select distinct
                                     floor(MONTHS_BETWEEN(TO_DATE(dados.\"Data Ingresso\",'DD-MM-YYYY'), dados.DT_NASCIMENTO) / 12)  AS IDADE,
                                     dados.*
                                from dados left join afast on afast.ID_SERVIDOR = dados.ID_SERVIDOR");

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
            $sql = DB::select("WITH DADOS AS (
                                    SELECT  DISTINCT TRIM(CAR.DESCRICAO_CARGO)   AS \"DESCRICAO_DO_CARGO\",
                                         DAD.CODIGO_MATRICULA AS \"MATRICULA_SIAPE\",
                                         TRIM(DAD.NOME_SERVIDOR)    AS \"NOME_DO_SERVIDOR\",
                                         DOC.NR_DOCUMENTACAO                  AS CPF_SERVIDOR,

                                         DAD.DATA_RESCISAO,
                                         SER.DT_NASCIMENTO,
                                         DAD.RESCICAO_RAIS    AS \"RAIS_RESCISAO_DESCRICAO\",
                                         CAR.ANO_CONCURSO      AS \"CONCURSO_ANO\",
                                         CASE SER.CD_SEXO
                                             WHEN 'M' THEN 'MASCULINO'
                                             ELSE 'FEMININO'
                                             END as SEXO,
                                         MOV.ID_SERVIDOR,
                                         LOT.CD_LOTACAO                       AS \"CODIGO UNIDADE EXERCICIO\",
                                         UF.SG_UF                             AS UF,
                                         MU.NM_MUNICIPIO || ' - ' || UF.SG_UF AS \"CIDADE DA UNIDADE\",
                                         NI.DS_NIVEL                          AS \"NIVEL\",
                                         RJ.DS_REGIME_JURIDICO                AS \"REGIME JURIDICO\",
                                         TRIM(TS.DS_TIPO_SERVIDOR)                  AS \"SITUACAO FUNCIONAL\",
                                         LT.SG_ORGAO                          AS \"ORGAO DE ORIGEM\"
        FROM VW_REL_CARGOEFETIVO CAR
        JOIN AGU_RH.DOCUMENTACAO DOC ON DOC.ID_SERVIDOR = CAR.ID_SERVIDOR AND DOC.ID_TIPO_DOCUMENTACAO = 1
        RIGHT JOIN VW_REL_DADOFUNCIONAL DAD ON DAD.ID_SERVIDOR = CAR.ID_SERVIDOR
        LEFT JOIN AGU_RH.SERVIDOR SER ON SER.ID_SERVIDOR = CAR.ID_SERVIDOR
        LEFT JOIN AGU_RH.CESSAO CES ON CES.ID_SERVIDOR = SER.ID_SERVIDOR
        LEFT JOIN AGU_RH.REGIME_JURIDICO RJ ON RJ.ID_REGIME_JURIDICO = CES.ID_REGIME_JURIDICO_DESTINO
        LEFT JOIN AGU_RH.TIPO_SERVIDOR TS ON SER.ID_TIPO_SERVIDOR = TS.ID_TIPO_SERVIDOR
        LEFT JOIN (
        SELECT U.ID_SERVIDOR, U.ULTIMA, U.ID_LOTACAO_EXERCICIO
        FROM (
        SELECT MAX(ID_MOVIMENTACAO) ULTIMA, ID_SERVIDOR, ID_LOTACAO_EXERCICIO
        FROM MOVIMENTACAO
        GROUP BY ID_SERVIDOR, ID_LOTACAO_EXERCICIO
        ) U
        LEFT JOIN MOVIMENTACAO N ON N.ID_MOVIMENTACAO = U.ULTIMA
        LEFT JOIN ORGAO O ON O.ID_ORGAO = N.ID_ORGAO_MOVIMENTACAO
        )
        MOV ON MOV.ID_SERVIDOR = SER.ID_SERVIDOR
        LEFT JOIN AGU_RH.LOTACAO LOT ON LOT.ID_LOTACAO = MOV.ID_LOTACAO_EXERCICIO
        LEFT JOIN AGU_RH.ENDERECO EN ON EN.ID_ENDERECO = LOT.ID_ENDERECO
        LEFT JOIN AGU_RH.MUNICIPIO MU ON MU.ID_MUNICIPIO = EN.ID_MUNICIPIO
        LEFT JOIN AGU_RH.UF UF ON MU.ID_UF = UF.ID_UF
        JOIN AGU_RH.CARGO_EFETIVO CE ON CE.ID_SERVIDOR = SER.ID_SERVIDOR
        JOIN AGU_RH.CARGO CA ON CA.ID_CARGO = CE.ID_CARGO
        LEFT JOIN NIVEL NI ON CA.ID_NIVEL = NI.ID_NIVEL
        LEFT JOIN AGU_RH.AFASTAMENTO A ON A.ID_SERVIDOR = SER.ID_SERVIDOR
        JOIN ( SELECT U.ID_SERVIDOR,
        O.DS_ORGAO || ' - ' || O.SG_ORGAO AS SG_ORGAO
        FROM (
        SELECT MAX(ID_MOVIMENTACAO) ULTIMA, ID_SERVIDOR
        FROM MOVIMENTACAO
        GROUP BY ID_SERVIDOR
        ) U
        LEFT JOIN MOVIMENTACAO N ON N.ID_MOVIMENTACAO = U.ULTIMA
        LEFT JOIN ORGAO O ON O.ID_ORGAO = N.ID_ORGAO_MOVIMENTACAO ) LT ON LT.ID_SERVIDOR = SER.ID_SERVIDOR
        WHERE (DAD.DATA_RESCISAO IS NOT NULL)
        AND DAD.ID_RH = 1
        ),
        AFAST AS (
        SELECT ID_SERVIDOR,MAX(DT_FIM_AFASTAMENTO) AS DT_FIM_AFASTAMENTO FROM AGU_RH.AFASTAMENTO GROUP BY ID_SERVIDOR
        )
        SELECT DISTINCT
		 floor(MONTHS_BETWEEN(DADOS.DATA_RESCISAO, DT_NASCIMENTO) / 12)  AS IDADE,
		 TO_CHAR(DADOS.DATA_RESCISAO, 'DD/MM/YYYY') AS DT_RESCISAO,
              TO_CHAR(DADOS.DT_NASCIMENTO, 'DD/MM/YYYY') AS DT_NASCIMENTO,
            DADOS.*

        FROM DADOS LEFT JOIN AFAST ON AFAST.ID_SERVIDOR = DADOS.ID_SERVIDOR");
            DB::commit();
            return $sql;
        } catch (\Exception $e) {
            RETURN $e->getMessage();
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
                                        SELECT DISTINCT
                                            PAI.CD_LOTACAO,
                                            TRIM(PAI.SG_LOTACAO) as DS_LOTACAO_PAI,
                                            LOTACAO.CD_LOTACAO,
                                            TRIM(LOTACAO.SG_LOTACAO),
                                            LOTACAO.CD_SIORG,

                                            TRIM(LOTACAO.DS_LOTACAO) AS DS_LOTACAO,
                                            TRIM(PAI.DS_LOTACAO) AS DS_LOTACAO_PAI1,

                                            CASE
                                                WHEN LOTACAO.IN_ATIVO = 1 THEN 'Sim'
                                                WHEN LOTACAO.IN_ATIVO = 0 THEN 'Não'
                                                ELSE ''
                                                END  AS ATIVO,
                                            LOTACAO.CD_UORG,
                                            TO_CHAR(LOTACAO.DT_CRIACAO_LOTACAO, 'DD/MM/YYYY') AS DT_CRIACAO_LOTACAO,
                                            TO_CHAR(LOTACAO.DT_EXTINCAO_LOTACAO, 'DD/MM/YYYY') AS DT_EXTINCAO_LOTACAO,
                                            TIPO_LOTACAO.DS_TIPO_LOTACAO,

                                            LOTACAO.ID_SERVIDOR_TITULAR AS ID_SERVIDOR_TITULAR,
                                            TRIM(AGU_RH.SERVIDOR.NM_SERVIDOR) AS NM_SERVIDOR_TITULAR,
                                            S.ID_SERVIDOR AS ID_SERVIDOR_SUBSTITUTO,
                                            TRIM(S.NM_SERVIDOR) AS NM_SERVIDOR_SUBSTITUTO,

                                            TELEFONE.NR_DDD  AS DDD,
                                            TELEFONE.NR_TELEFONE AS TELEFONE,

                                            MUNICIPIO.NM_MUNICIPIO AS MUNICIPIO,
                                            TRIM(ENDERECO.DS_ENDERECO) AS ENDERECO,
                                            TRIM(ENDERECO.NM_BAIRRO)  AS BAIRRO,
                                            TRIM(ENDERECO.NR_CEP)  AS CEP,
                                            TRIM(ENDERECO.DS_COMPLEMENTO)  AS COMPLEMENTO,
                                            UF.SG_UF AS UF,
                                            TRIM(LOTACAO.NM_EMAIL_LOTACAO),
                                            TO_CHAR(LOTACAO.DT_INICIO_UDP, 'DD/MM/YYYY') AS DT_INICIO_UDP,
                                            TO_CHAR(LOTACAO.DT_EXPIRACAO_UDP, 'DD/MM/YYYY') AS DT_EXPIRACAO_UDP,
                                            CASE
                                                WHEN LOTACAO.IN_TIPO_NORMA_UDP = 'F' THEN 'Não'
                                                WHEN LOTACAO.IN_TIPO_NORMA_UDP = 'I' THEN 'Sim'
                                                ELSE ''
                                                END  AS IN_TIPO_NORMA_UDP,
                                            CASE
                                                WHEN LOTACAO.IN_TIPO_NORMA_ODS = 'I' THEN 'Não'
                                                WHEN LOTACAO.IN_TIPO_NORMA_ODS = 'F' THEN 'Sim'
                                                ELSE ''
                                                END  AS IN_TIPO_NORMA_ODS

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
                                        ORDER BY LOTACAO.CD_LOTACAO ASC
                                        ");
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
                                           WHEN TRIM(AA.DS_LOTACAO) IS NULL THEN
                                               TRIM(AA.CD_LOTACAO)
                                           ELSE
                                               TRIM(AA.CD_LOTACAO) || ' - ' || TRIM(AA.DS_LOTACAO)
                                           END                                               AS LOTACAO,
                                       CASE
                                           WHEN TRIM(AA.DS_CARGO_FUNCAO) IS NULL THEN
                                               TRIM(AA.CD_CARGO_FUNCAO)
                                           ELSE
                                               TRIM(AA.CD_CARGO_FUNCAO) || ' - ' || TRIM(AA.DS_CARGO_FUNCAO)
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
                                         SELECT DISTINCT CF.ID_FUNCAO_GRATIFICADA,
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
