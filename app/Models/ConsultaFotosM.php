<?php
namespace App\Models;

use CodeIgniter\Model;

class ConsultaFotosM extends Model
{
    protected $table         = 'consulta_fotos';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['consulta_id', 'nombre_imagen', 'ruta_imagen', 'tipo_mime'];
    protected $useTimestamps = true;
}
