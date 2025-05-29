<?php

namespace App\Models;
use CodeIgniter\Model;

class AuditoriaM extends Model
{
    protected $table = 'auditoria';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'usuario_id', 'accion', 'tabla_afectada', 'registro_id', 
        'datos_anteriores', 'datos_nuevos', 'ip', 'user_agent'
    ];
}
