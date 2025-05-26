<?php

namespace App\Models;

use CodeIgniter\Model;

class UsuariosM extends Model
{
    protected $table         = 'usuarios';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'nombre', 'email', 'telefono', 'password_hash', 'role_id', 'activo'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'creado_at';
    protected $updatedField  = 'actualizado_at';
}