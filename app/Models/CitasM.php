<?php

namespace App\Models;

use CodeIgniter\Model;

class CitasM extends Model
{
    protected $table         = 'citas';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'paciente_id', 'medico_id', 'fecha_inicio', 'fecha_fin', 'estado', 'creado_por'
    ];

    protected $useTimestamps = false;
}
