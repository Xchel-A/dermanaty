<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UsuariosM;
use App\Models\RolesM;


/**
 * Controlador de usuarios: login + CRUD
 * - Redirección de vistas según rol (admin, médico, recepcionista)
 * - CRUD completo de usuarios (solo accesible para rol Administracion)
 */
class Usuarios extends BaseController
{
    protected UsuariosM $usuarios;
    protected RolesM    $roles;
    protected $session;

    public function __construct()
    {
        $this->usuarios = new UsuariosM();
        $this->roles    = new RolesM();
        $this->session  = session();
    }

    /*=================================================
    |  LOGIN / LOGOUT                                 |
    =================================================*/
    public function login()
    {
        if ($this->request->getMethod() === 'post') {
            return $this->attemptLogin();
        }
        return view('auth/login');
    }

    private function attemptLogin()
    {
        $rules = [
            'email'    => 'required|valid_email',
            'password' => 'required',
        ];
        if (! $this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $email    = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $user = $this->usuarios
            ->where(['email' => $email, 'activo' => 1])
            ->first();

        if (! $user || ! password_verify($password, $user['password_hash'])) {
            return redirect()->back()->with('error', 'Credenciales no válidas');
        }

        // guarda datos mínimos en sesión
        $this->session->set([
            'user_id'   => $user['id'],
            'user_name' => $user['nombre'],
            'role_id'   => $user['role_id'],
            'isLogged'  => true,
        ]);

        // redirección por rol
        return match($user['role_id']) {
            1       => redirect()->to('/admin/dashboard'),         // Administracion
            2       => redirect()->to('/medico/dashboard'),        // Médico
            3       => redirect()->to('/recepcionista/dashboard'), // Recepcionista
            default => redirect()->to('/'),
        };
    }

    public function logout()
    {
        $this->session->destroy();
        return redirect()->to('/login');
    }

    /*=================================================
    |  DASHBOARD/ ADMIN , MEDICO , RECEPCIONISTA   |
    =================================================*/
    public function admin()
    {
        return view('dashboards/admin', [
            'title' => 'Panel de Administración',
        ]);
    }

    public function medico()
    {
        return view('dashboards/medico', [
            'title' => 'Panel del Médico',
        ]);
    }

    public function recepcionista()
    {
        return view('dashboards/recepcionista', [
            'title' => 'Panel de Recepción',
        ]);
    }
    /*=================================================
    |  CRUD USUARIOS (solo Administracion)            |
    =================================================*/
    public function index()
    {
        $users = $this->usuarios
            ->select('usuarios.*, roles.nombre AS rol')
            ->join('roles', 'roles.id = usuarios.role_id')
            ->orderBy('usuarios.id', 'DESC')
            ->findAll();

        return view('usuarios/index', compact('users'));
    }

    public function create()
    {
        $roles = $this->roles->findAll();
        return view('usuarios/create', compact('roles'));
    }

    public function store()
    {
        $rules = [
            'nombre'   => 'required|min_length[3]',
            'email'    => 'required|valid_email|is_unique[usuarios.email]',
            'telefono' => 'permit_empty|regex_match[/^[0-9\+\-\s]+$/]',
            'password' => 'required|min_length[6]',
            'role_id'  => 'required|is_not_unique[roles.id]'
        ];
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->usuarios->insert([
            'nombre'        => $this->request->getPost('nombre'),
            'email'         => $this->request->getPost('email'),
            'telefono'      => $this->request->getPost('telefono'),
            'password_hash' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'role_id'       => $this->request->getPost('role_id'),
        ]);

        return redirect()->to('/usuarios')->with('message', 'Usuario creado exitosamente');
    }

    public function edit(int $id)
    {
        $user  = $this->usuarios->find($id);
        $roles = $this->roles->findAll();
        return view('usuarios/edit', compact('user', 'roles'));
    }

    public function update(int $id)
    {
        $rules = [
            'nombre'   => 'required|min_length[3]',
            'email'    => "required|valid_email|is_unique[usuarios.email,id,{$id}]",
            'telefono' => 'permit_empty|regex_match[/^[0-9\+\-\s]+$/]',
            'role_id'  => 'required|is_not_unique[roles.id]'
        ];
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'nombre'   => $this->request->getPost('nombre'),
            'email'    => $this->request->getPost('email'),
            'telefono' => $this->request->getPost('telefono'),
            'role_id'  => $this->request->getPost('role_id'),
        ];
        if ($pass = $this->request->getPost('password')) {
            $data['password_hash'] = password_hash($pass, PASSWORD_DEFAULT);
        }

        $this->usuarios->update($id, $data);
        return redirect()->to('/usuarios')->with('message', 'Usuario actualizado');
    }

    public function delete(int $id)
    {
        $this->usuarios->delete($id);
        return redirect()->to('/usuarios')->with('message', 'Usuario eliminado');
    }
}

