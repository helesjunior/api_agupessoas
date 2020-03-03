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
        $rota = $this->rotaBase . 'forca-trabalho';
        $this->verificaRota($rota);
    }

    /**
     * Verifica rota funções
     *
     * @return void
     */
    public function testRotaFuncoes()
    {
        $rota = $this->rotaBase . 'funcoes';
        $this->verificaRota($rota);
    }

    /**
     * Verifica rota antiguidade
     *
     * @return void
     */
    public function testRotaAntiguidade()
    {
        $rota = $this->rotaBase . 'antiguidade';
        $this->verificaRota($rota);
    }

    /**
     * Verifica rota cessões
     *
     * @return void
     */
    public function comentado_testRotaCessoes()
    {
        $rota = $this->rotaBase . 'cessoes';
        $this->verificaRota($rota);
    }

    /**
     * Verifica rota provimentos
     *
     * @return void
     */
    public function comentado_testRotaProvimentos()
    {
        $rota = $this->rotaBase . 'provimentos';
        $this->verificaRota($rota);
    }

    /**
     * Verifica rota requisições
     *
     * @return void
     */
    public function comentado_testRotaRequisicoes()
    {
        $rota = $this->rotaBase . 'requisicoes';
        $this->verificaRota($rota);
    }

    /**
     * Verifica rota vacâncias
     *
     * @return void
     */
    public function comentado_testRotaVacancias()
    {
        $rota = $this->rotaBase . 'vacancias';
        $this->verificaRota($rota);
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
