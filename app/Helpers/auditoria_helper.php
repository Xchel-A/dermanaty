<?php

use App\Models\AuditoriaM;

if (!function_exists('registrarAuditoria')) {
    function registrarAuditoria(string $accion, string $tabla, $registro_id = null, $datos_anteriores = null, $datos_nuevos = null)
    {
        $auditoria = new AuditoriaM();

        $usuario_id = session('usuario_id') ?? null;
        $ip = service('request')->getIPAddress();
        $userAgent = service('request')->getUserAgent()->getAgentString();

        $auditoria->insert([
            'usuario_id'       => $usuario_id,
            'accion'           => $accion,
            'tabla_afectada'   => $tabla,
            'registro_id'      => $registro_id,
            'datos_anteriores' => $datos_anteriores ? json_encode($datos_anteriores) : null,
            'datos_nuevos'     => $datos_nuevos ? json_encode($datos_nuevos) : null,
            'ip'               => $ip,
            'user_agent'       => $userAgent,
        ]);
    }
}
