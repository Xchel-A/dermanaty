<?php
namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class SessionRoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $roleId = session('role_id');

        if (!$roleId) {
            return redirect()->to('/login');
        }

        // Si no hay argumentos definidos, se permite el acceso
        if (!$arguments) {
            return;
        }

        // Verificamos si el rol del usuario está dentro de los permitidos
        foreach ($arguments as $requiredRole) {
            if ((int) $roleId === (int) $requiredRole) {
                return; // Acceso permitido
            }
        }

        // No tiene permisos
        return redirect()->to('/login');
    }


    public function after(
        RequestInterface $request,
        ResponseInterface $response,
        $arguments = null
    ) {
        // no-op
    }
}
