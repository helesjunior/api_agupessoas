<?php

namespace App\Models;

use DB;
use Yajra\Oci8\Oci8Connection;
use Illuminate\Database\Eloquent\Model;

class Base extends Model
{

    /**
     * Retorna dados vindos do cursor de dada procedure
     *
     * @param string $package
     * @param string $procedure
     * @param array $params
     * @return array
     */
    protected function retornaDadosPorCursorDeProcedure($package = '', $procedure = '', $params = [])
    {
        $result = '';
        $packageProcedure = $this->retornaPackageProcedure($package, $procedure);

        $pdo = DB::getPdo();
        $oracle = new Oci8Connection($pdo);

        if ($packageProcedure != '') {
            $result = $oracle->executeProcedureWithCursor($packageProcedure, $params);
        }

        return $result;
    }

    /**
     * Retorna nome conjunto contendo $package e $procedure, sendo que este último é obrigatório
     *
     * @param string $package
     * @param string $procedure
     * @return string
     */
    protected function retornaPackageProcedure($package = '', $procedure = '')
    {
        if ($procedure == '') {
            return '';
        }

        $packageProcedure = ' ';
        $packageProcedure .= ($package != '' ? $package . '.' : '');
        $packageProcedure .= $procedure;
        $packageProcedure .= ' ';

        return $packageProcedure;
    }

}
