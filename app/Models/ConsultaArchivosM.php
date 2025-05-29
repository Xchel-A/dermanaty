<?php

namespace App\Models;

use CodeIgniter\Model;

class ConsultaArchivosM extends Model
{
    protected $table         = 'consulta_archivos';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['consulta_id', 'nombre_archivo', 'ruta_archivo', 'tipo_mime'];
    protected $useTimestamps = true;
}
