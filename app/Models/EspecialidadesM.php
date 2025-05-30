<?php

namespace App\Models;

use CodeIgniter\Model;

class EspecialidadesM extends Model
{
    protected $table         = 'especialidades';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'nombre', 'activo', 'creado_at', 'actualizado_at'
    ];

    protected $useTimestamps = false;
}
