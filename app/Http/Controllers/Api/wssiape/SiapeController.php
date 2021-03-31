<?php

namespace App\Http\Controllers\Api\wssiape;

use App\Models\ConcessaoFerias;
use Illuminate\Support\Facades\DB;
use SoapHeader;
use SoapVar;
use SoapClient;
use PHPRtfLite;
use PHPRtfLite_Font;



class SiapeController
{
    /*
    Ambiente de homologação:
    http://hom1.siapenet.gov.br/WSSiapenet/services/ConsultaSIAPE?wsdl

    Ambiente de produção:
    https://www1.siapenet.gov.br/WSSiapenet/services/ConsultaSIAPE?wsdl

    private const string OBSERVACAOPADRAO = "DADOS ATUALIZADOS VIA IMPORTAÇÃO DE DADOS DO WEBSERVICE - SIAPE. DATA: {0}";
    private const string CPFOPERADOR = "32496273134";

    private const string WSSIGLASISTEMA = "AGUPessoas";
    private const string WSNOMESISTEMA = "AGUPessoas";
    private const string WSSENHA = "58C300S6";
    private const string WSCODORGAO = "40106";
    private const string WSEXISTPAG = "b";
    private const string WSTIPOVINCULO = "c";

    WSSiape.ConsultaSIAPE svcSiape = new WSSiape.ConsultaSIAPE();
    WSSiape.ArrayDadosAfastamento afastamentos = svcSiape.consultaDadosAfastamento(WSSIGLASISTEMA, WSNOMESISTEMA, WSSENHA, nrCpf, WSCODORGAO, WSEXISTPAG, WSTIPOVINCULO);

    */

    private $soapClient;
    private $Urlwsdl = 'https://www1.siapenet.gov.br/WSSiapenet/services/ConsultaSIAPE?wsdl';

    public function __construct()
    {
        $this->soapClient = new SoapClient($this->Urlwsdl);
    }

    public function consultaServidorSiape()
    {
        $servidoresAtivos = $this->consultaTodosServidoresAtivos();

        foreach ($servidoresAtivos as $v){

            $idServidor = $v->id_servidor;
            $cpfServidor = $v->cpf_servidor;


            $dadosFuncionais = $this->soapClient->consultaDadosFuncionais
            ('AGUPessoas', 'AGUPessoas', '58C300S6', $cpfServidor, '40106', 'b', 'c');
            $dadosEscolaridades = $this->soapClient->consultaDadosEscolares
            ('AGUPessoas', 'AGUPessoas', '58C300S6', $cpfServidor, '40106', 'b', 'c');


            $dadosF = [];
            foreach ($dadosFuncionais->dadosFuncionais as $dados ){

                dd($dados);
            }
            $retorno =  [

            ];



        }


    }

    public function consultaTodosServidoresAtivos(){
        try {

            $result = DB::table('AGU_RH.SERVIDOR S')
                ->join('AGU_RH.DOCUMENTACAO D', 'S.ID_SERVIDOR', '=', 'D.ID_SERVIDOR')
                ->where('S.IN_STATUS_SERVIDOR', 1)
                ->where('D.ID_TIPO_DOCUMENTACAO', 1)
                ->orderBy('D.NR_DOCUMENTACAO')
                ->select('S.ID_SERVIDOR', 'D.NR_DOCUMENTACAO as CPF_SERVIDOR');
            return $result->take(1)->get();

        } catch (\Exception $e) {
            return $e->getMessage();
            //return ['error', 'Ocorreu um erro no carregamento de dados, por favor tente novamente.'];
        }

    }


}

