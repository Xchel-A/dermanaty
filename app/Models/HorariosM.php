<?php

namespace App\Models;

use CodeIgniter\Model;

class HorariosM extends Model
{
    protected $table         = 'horarios';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'medico_id', 'dia_semana', 'hora_inicio', 'hora_fin'
    ];

    protected $useTimestamps = false;
}
