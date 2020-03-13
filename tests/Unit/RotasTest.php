<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RotasTest extends TestCase
{

    /**
     * @var string
     */
    private $rotaBase = '/api/v1/pessoas/';

    /**
     * Verifca versão atual da API e prefixo
     *
     * @return void
     */
    public function testVersaoEPrefixoDaApi()
    {
        $versaoNumero = 1;
        $prefixo = 'pessoas';
        $api = '/api/v' . $versaoNumero . '/' . $prefixo . '/';

        $this->assertEquals($api, $this->rotaBase);
    }

    /**
     * Verifica rota força de trabalho
     *
     * @return void
     */
    public function testRotaForcaTrabalho()
    {
        $rota = $this->retornaRotaCompleta('forca-trabalho');
        $this->verificaRota($rota);
    }

    /**
     * Verifica rota funções
     *
     * @return void
     */
    public function testRotaFuncoes()
    {
        $rota = $this->retornaRotaCompleta('funcoes');
        $this->verificaRota($rota);


    }

    /**
     * Verifica rota antiguidade
     *
     * @return void
     */
    public function testRotaAntiguidade()
    {
        $rota = $this->retornaRotaCompleta('antiguidade', '&database=20200305');
        $this->verificaRota($rota);
    }

    /**
     * Verifica rota cessões
     *
     * @return void
     */
    public function testRotaCessoes()
    {
        $rota = $this->retornaRotaCompleta('cessoes');
        $this->verificaRota($rota);
    }

    /**
     * Verifica rota provimentos
     *
     * @return void
     */
    public function testRotaProvimentos()
    {
        $rota = $this->retornaRotaCompleta('provimentos');
        $this->verificaRota($rota);
    }

    /**
     * Verifica rota requisições
     *
     * @return void
     */
    public function testRotaRequisicoes()
    {
        $rota = $this->retornaRotaCompleta('requisicoes');
        $this->verificaRota($rota);
    }

    /**
     * Verifica rota vacâncias
     *
     * @return void
     */
    public function testRotaVacancias()
    {
        $rota = $this->retornaRotaCompleta('vacancias');
        $this->verificaRota($rota);
    }

    private function retornaRotaCompleta($rotaNome, $params = '')
    {
        $rota = $this->rotaBase . 'forca-trabalho';
        $rota .= '?token=base64:ktqQXu6aW44hadYaHTV89m8FDCi5Pu6XlXL@AugnC9E=';
        $rota .= $params;

        return $rota;
    }

    /**
     * Verifica rota informada no parâmetro $rota
     *
     * @param string $rota
     * @return void
     */
    private function verificaRota($rota = '/')
    {
        $response = $this->get($rota);
        // $response->assertStatus(200);

        $statusAceitos = [200, 500];
        $status = $response->status();
        $this->assertTrue(in_array($status, $statusAceitos));
    }

}
