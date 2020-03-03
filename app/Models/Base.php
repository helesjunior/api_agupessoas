<?php

namespace App\Models;

use DB;
use Illuminate\Database\Eloquent\Model;
use Yajra\Oci8\Oci8Connection;

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
    public function retornaDadosPorCursorDeProcedure($package = '', $procedure = '', $params = [])
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
    public function retornaPackageProcedure($package = '', $procedure = '')
    {
        if ($procedure == '') {
            return '';
        }

        $packageProcedure = '';
        $packageProcedure .= ($package != '' ? $package . '.' : '');
        $packageProcedure .= $procedure;

        return $packageProcedure;
    }

    /**
     * Converte array recursivamente, formatando string para UTF-8
     *
     * @param array $dados
     * @return array
     */
    public function converteArrayDadosUtf8($dados = [])
    {
        if (!is_array($dados)) {
            return $dados;
        }

        $dadosUtf8 = [];

        foreach ($dados as $registro => $campos) {
            if (is_object($campos)) {
                $campos = (array) $campos;
            }

            if (is_array($campos)) {
                foreach ($campos as $chave => $valor) {
                    $dadosUtf8[$registro][$chave] = mb_convert_encoding($valor, 'UTF-8', 'auto');;
                }
            }
        }

        return $dadosUtf8;
    }

}
