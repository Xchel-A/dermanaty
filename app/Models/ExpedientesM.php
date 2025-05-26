<?php

namespace App\Models;

use CodeIgniter\Model;

class ExpedientesM extends Model
{
    protected $table         = 'expedientes';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'paciente_id', 'creado_por', 'fecha_apertura', 'motivo_apertura', 'estado'
    ];

    protected $useTimestamps = false;
}
