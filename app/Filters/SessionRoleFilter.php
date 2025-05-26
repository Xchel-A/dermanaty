<?php
namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class SessionRoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $required = $arguments[0] ?? null;        // '1', '2', '3'…
        $roleId   = session('role_id');           // guardado en UsuariosController

        if ($required === null) {
            return;                               // nada que comprobar
        }

        // Igualamos a int para comparación segura
        if ((int) $roleId === (int) $required) {
            return;                               // acceso permitido
        }

        // No coincide ⇒ fuera
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
