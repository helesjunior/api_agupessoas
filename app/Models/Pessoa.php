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
     * Retorna Listagem contendo dados para o ConectaTCU
     *
     * @return array
     * @author Ramon Ladeia <ramon.ladeia@agu.gov.br>
     * @author Celso da silva couto junior <celso.couto@agu.gov.br>
     */

    public function retornaConectaTCU($cpf)
    {
       $result =  DB::table('SERVIDOR')
            ->join('DOCUMENTACAO', 'DOCUMENTACAO.ID_SERVIDOR', '=', 'SERVIDOR.ID_SERVIDOR')
            ->leftJoin('DADO_FUNCIONAL', 'DADO_FUNCIONAL.ID_SERVIDOR', '=', 'SERVIDOR.ID_SERVIDOR')
            ->leftJoin('cargo_efetivo', 'cargo_efetivo.id_servidor', '=', 'servidor.id_servidor')
            ->leftJoin('cargo', 'cargo.id_cargo', '=', 'cargo_efetivo.id_cargo')
            ->select('SERVIDOR.NM_SERVIDOR as nome do servidor',
                'DOCUMENTACAO.NR_DOCUMENTACAO as cpf',
                DB::raw( "CASE
                            WHEN cargo.cd_cargo_rh IN ('410001','410004','R410004','414001','414017','R414017')
                                THEN 'ADVOGADO DA UNIÃO'
                            WHEN cargo.CD_CARGO_RH IN ('408001','408002','R408001','R408002')
                                THEN 'PROCURADOR FEDERAL'
                            ELSE 'SERVIDOR'
                          END  AS CARREIRA"),
                DB::raw( "CASE
                            WHEN  servidor.in_status_servidor ='1' THEN 'ATIVO'
                            ELSE 'INATIVO'
                          END  AS STATUS"),

                'DADO_FUNCIONAL.CD_MATRICULA_SIAPE as matricula_siape',
                'cargo.cd_cargo_rh as codigo do cargo',
                'cargo.ds_cargo_rh as nome do cargo',
                 DB::raw('SYSDATE as consultado_em')
            )
            ->where('DOCUMENTACAO.NR_DOCUMENTACAO',$cpf)
           ->whereIn('CD_CARGO_RH', [410001,410004,'R410004',414001,414017,'R414017',408001,408002,'R408001','R408002'])
            ->first();

        return $result;

    }


    /**
     * Retorna Listagem contendo dados para o gerarRelatorioCovidTxt
     *
     * @return array
     * @author Thiago Mariano <thiago.damasceno@agu.gov.br>
     * @data 11/12/2020
     */

    public function gerarRelatorioCovidTxt()
    {
        ini_set("memory_limit", "512M");
        try {

            $this->gerarRelatorioCovidXls();


            DB::beginTransaction();
            $results = DB::select("SELECT '0' || '40106' || '202010' || '001' || RPAD(' ',40) arquivo_covid_txt FROM DUAL
                                        UNION
                                        SELECT '1' ||
                                               '41691636134' ||
                                               '40106' ||
                                               LPAD(V74_COD_SIAPE, 7, 0) ||
                                               T06_ANO ||
                                               T06_MES ||
                                               T06_DIA_INICIO ||
                                               T06_ANO ||
                                               T06_MES ||
                                               T06_DIA_FIM ||
                                               CASE WHEN T06_COD_AFASTAMENTO IN ('7795', '7796', '7797', '7798', '7799', '7800', '7996') THEN
                                                        '0387'
                                                    ELSE
                                                        '0388'
                                                   END ||
                                               RPAD(' ',11) arquivo_covid_txt
                                        FROM XAGU_RH.T06_FREQ_SERVIDOR
                                                 JOIN XAGU_RH.T05_FREQ_UNIDADE
                                                      ON T06_ANO = T05_ANO
                                                          AND T06_MES = T05_MES
                                                          AND T06_COD_UNIDADE = T05_COD_UNIDADE
                                                 JOIN XAGU_ESTRUTURA.V74_PESSOAS
                                                      ON T06_NU_CPF = V74_NUM_CPF
                                                 LEFT JOIN XAGU_ESTRUTURA.V86_UNIDADES
                                                           ON T06_COD_UNIDADE_FILHA = V86_COD_UNIDADE
                                        WHERE T06_ANO = '2020'
                                          AND T06_MES = '10'
                                          AND T06_COD_AFASTAMENTO IN ('7795',
                                                                      '7796',
                                                                      '7797',
                                                                      '7798',
                                                                      '7799',
                                                                      '7800',
                                                                      '8195',
                                                                      '8595',
                                                                      '7996')
                                          AND (T05_SN_RH = 'E'
                                            OR T05_SN_RH = 'R')
                                            /*administrativos*/
                                          --AND V74_COD_CARGO NOT IN ('408001', '410001', '410004', '414001')
                                            /*membros*/
                                          AND V74_COD_CARGO IN ('408001', '408002', '410001', '410002', '011002', '410004', '414001', '414017')
                                        UNION
                                        SELECT '9' || LPAD(COUNT(*), 6, '0') ||  RPAD(' ',48) arquivo_covid_txt
                                        FROM XAGU_RH.T06_FREQ_SERVIDOR
                                                 JOIN XAGU_RH.T05_FREQ_UNIDADE
                                                      ON T06_ANO = T05_ANO
                                                          AND T06_MES = T05_MES
                                                          AND T06_COD_UNIDADE = T05_COD_UNIDADE
                                                 JOIN XAGU_ESTRUTURA.V74_PESSOAS
                                                      ON T06_NU_CPF = V74_NUM_CPF
                                                 LEFT JOIN XAGU_ESTRUTURA.V86_UNIDADES
                                                           ON T06_COD_UNIDADE_FILHA = V86_COD_UNIDADE
                                        WHERE T06_ANO = '2020'
                                          AND T06_MES = '10'
                                          AND T06_COD_AFASTAMENTO IN ('7795',
                                                                      '7796',
                                                                      '7797',
                                                                      '7798',
                                                                      '7799',
                                                                      '7800',
                                                                      '8195',
                                                                      '8595',
                                                                      '7996')
                                          AND (T05_SN_RH = 'E'
                                            OR T05_SN_RH = 'R')
                                            /*administrativos*/
                                          --AND V74_COD_CARGO NOT IN ('408001', '410001', '410004', '414001')
                                            /*membros*/
                                          AND V74_COD_CARGO IN ('408001', '408002', '410001', '410002', '011002', '410004', '414001', '414017')
                                        order by arquivo_covid_txt");

            $out = fopen( 'testes.txt', 'w' );
            foreach ( $results as $result )
            {
                fputs( $out, $result->arquivo_covid_txt . "\n");
            }
            fclose( $out );

        } catch (\Exception $e) {
            return ['error', 'Ocorreu um erro no carregamento de dados, por favor tente novamente.'];
        }
    }

    /**
     * Retorna Listagem contendo dados para o gerarRelatorioCovidXls
     *
     * @return array
     * @author Thiago Mariano <thiago.damasceno@agu.gov.br>
     * @data 11/12/2020
     */

    public function gerarRelatorioCovidXls()
    {

        ini_set("memory_limit", "512M");
        try {

            DB::beginTransaction();
            $results = DB::select("
                                        SELECT V74_NUM_CPF,
                                               V74_NOME_SERVIDOR,
                                               V74_NOME_CARGO,
                                               V74_COD_SIAPE,
                                               T06_ANO,
                                               T06_MES,
                                               T06_DIA_INICIO,
                                               T06_ANO,
                                               T06_MES,
                                               T06_DIA_FIM,
                                               V74_DESC_NATUREZA
                                        FROM xagu_rh.T06_FREQ_SERVIDOR
                                                 JOIN xagu_rh.T05_FREQ_UNIDADE
                                                      ON T06_ANO = T05_ANO
                                                          AND T06_MES = T05_MES
                                                          AND T06_COD_UNIDADE = T05_COD_UNIDADE
                                                 JOIN xagu_estrutura.V74_PESSOAS
                                                      ON T06_NU_CPF = V74_NUM_CPF
                                                 LEFT JOIN xagu_estrutura.V86_UNIDADES
                                                           ON T06_COD_UNIDADE_FILHA = V86_COD_UNIDADE
                                        WHERE T06_ANO = '2020'
                                          AND T06_MES = '10'
                                          AND T06_COD_AFASTAMENTO IN ('7795',
                                                                      '7796',
                                                                      '7797',
                                                                      '7798',
                                                                      '7799',
                                                                      '7800',
                                                                      '8195',
                                                                      '8595',
                                                                      '7996')
                                          AND (T05_SN_RH = 'E'
                                            OR T05_SN_RH = 'R')
                                           /*administrativos*/
                                          --AND V74_COD_CARGO NOT IN ('408001', '410001', '410004', '414001')
                                          /*membros*/
                                          AND V74_COD_CARGO IN ('408001', '408002', '410001', '410002', '011002', '410004', '414001', '414017')
                                        ORDER BY V74_NOME_SERVIDOR");


            $objReader = PHPExcel::createReader('CSV');
            $objReader->setDelimiter(";"); // define que a separação dos dados é feita por ponto e vírgula
            $objReader->setInputEncoding('UTF-8'); // habilita os caracteres latinos.
            $objPHPExcel = $objReader->load('arquivo.csv'); //indica qual o arquivo CSV que será convertido
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $objWriter->save('arquivo.xls'); // Resultado da conversão; um arquivo do EXCEL

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
