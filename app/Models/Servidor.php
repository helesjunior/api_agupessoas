<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Servidor extends Model
{
    protected $table = 'SERVIDOR';

    protected $primaryKey = 'ID_SERVIDOR';

    public function documentacao()
    {
        return $this->hasMany(Documentacao::class,'ID_SERVIDOR');
    }

}
