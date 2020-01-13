<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Documentacao extends Model
{
    protected $table = 'DOCUMENTACAO';


    public function servidor()
    {
        return $this->belongsTo(Servidor::class, 'id_servidor');
    }

    public function tipoDocumentacao()
    {
        return $this->belongsTo(TipoDocumentacao::class, 'ID_TIPO_DOCUMENTACAO');
    }

}
