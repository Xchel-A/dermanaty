<?php
namespace App\Models;

use CodeIgniter\Model;

class RolesM extends Model
{
    protected $table         = 'roles';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['nombre', 'descripcion'];

    protected $useTimestamps = true;
    protected $createdField  = 'creado_at';
    protected $updatedField  = 'actualizado_at';
}
