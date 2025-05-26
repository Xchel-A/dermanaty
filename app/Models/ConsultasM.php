<?php

namespace App\Models;

use CodeIgniter\Model;

class ConsultasM extends Model
{
    protected $table         = 'consultas';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'expediente_id', 'medico_id', 'fecha_consulta', 'motivo', 'diagnostico', 'tratamiento', 'notas'
    ];

    protected $useTimestamps = false;
}