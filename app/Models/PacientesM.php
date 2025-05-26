<?php

namespace App\Models;

use CodeIgniter\Model;

class PacientesM extends Model
{
    protected $table         = 'pacientes';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'nombre', 'apellidos', 'fecha_nacimiento', 'genero', 'telefono', 'email', 'direccion'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'creado_at';
    protected $updatedField  = 'actualizado_at';
}