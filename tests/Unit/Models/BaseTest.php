<?php

namespace Tests\Unit\Controllers\Api\v1;

use App\Models\Base;
use Tests\TestCase;

class BaseTest extends TestCase
{

    public function testAgrupaNomePackageEProcedureAmbosParametrosVazios()
    {
        $base = new Base();

        // Sem $procedure, não volta nada
        $package = '';
        $procedure = '';
        $this->assertEquals('', $base->retornaPackageProcedure($package, $procedure));
    }

    public function testAgrupaNomePackageEProcedureParamProcedureInformado()
    {
        $base = new Base();

        // Sem $package retorna o nome da procedure
        $package = '';
        $procedure = 'PROCEDURE';
        $this->assertEquals('PROCEDURE', $base->retornaPackageProcedure($package, $procedure));
    }

    public function testAgrupaNomePackageEProcedureAmbosParametrosInformados()
    {
        $base = new Base();

        // Com $package e $procedure retorna ambos concatenados com [.] entre ambos
        $package = 'PACKAGE';
        $procedure = 'PROCEDURE';
        $this->assertEquals('PACKAGE.PROCEDURE', $base->retornaPackageProcedure($package, $procedure));
    }

}
