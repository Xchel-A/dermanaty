<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\EspecialidadesM;
use App\Models\HorariosM;
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
    protected RolesM $roles;

    protected HorariosM $horarios;

    protected EspecialidadesM $especialidades;
    protected $session;

    public function __construct()
    {
        $this->usuarios = new UsuariosM();
        $this->roles = new RolesM();
        $this->especialidades = new EspecialidadesM();
        $this->horarios = new HorariosM();
        // Cargar la sesión
        $this->session = session();
    }

    /*=================================================
    |  LOGIN / LOGOUT                                 |
    =================================================*/
    public function login()
    {
        echo $this->request->getMethod();
        if ($this->request->getMethod() === 'POST') {   // minúscula
            return $this->attemptLogin();               // procesa login
        }

        // GET: muestra el formulario
        return view('auth/login');
    }

    private function attemptLogin()
    {
        $rules = [
            'email' => 'required|valid_email',
            'password' => 'required',
        ];
        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $user = $this->usuarios
            ->where(['email' => $email, 'activo' => 1])
            ->first();

        if (!$user || !password_verify($password, $user['password_hash'])) {
            return redirect()->back()->with('error', 'Credenciales no válidas');
            echo "credenciales no validadas ";
        }
        echo "<pre>" . print_r($user, true) . "</pre>";

        // guarda datos mínimos en sesión
        $this->session->set([
            'user_id' => $user['id'],
            'user_name' => $user['nombre'],
            'role_id' => $user['role_id'],
            'isLogged' => true,
        ]);


        $role = (int) $user['role_id'];

        return match ($role) {
            1 => redirect()->to(base_url('/admin/dashboard')),
            2 => redirect()->to(base_url('/medico/dashboard')),
            3 => redirect()->to(base_url('/recepcionista/dashboard')),
            default => redirect()->to(base_url('/')),
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
            'title' => 'Panel de Administración'
        ]);
    }

    public function medico()
    {
        $medicoId = $this->session->get('user_id'); // o 'user_id', depende de cómo lo guardes
        return view('dashboards/medico', [
            'title' => 'Panel del Médico',
            'medicoId' => $medicoId,
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
            ->select('usuarios.*, roles.nombre AS rol, especialidades.nombre AS especialidad')
            ->join('roles', 'roles.id = usuarios.role_id')
            ->join('especialidades', 'especialidades.id = usuarios.especialidad_id', 'left') // LEFT JOIN por si hay usuarios sin especialidad
            ->orderBy('usuarios.id', 'DESC')
            ->findAll();

        return view('usuarios/index', compact('users'));
    }


    public function create()
    {
        $roles = $this->roles->findAll();
        $especialidades = $this->especialidades->findAll(); // Si necesitas especialidades

        return view('usuarios/create', compact('roles', 'especialidades'));
    }

    public function store()
    {
        $rules = [
            'nombre' => 'required|min_length[3]',
            'email' => 'required|valid_email|is_unique[usuarios.email]',
            'telefono' => 'permit_empty|regex_match[/^[0-9\+\-\s]+$/]',
            'password' => 'required|min_length[6]',
            'role_id' => 'required|is_not_unique[roles.id]'
        ];
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->usuarios->insert([
            'nombre' => $this->request->getPost('nombre'),
            'email' => $this->request->getPost('email'),
            'telefono' => $this->request->getPost('telefono'),
            'password_hash' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'role_id' => $this->request->getPost('role_id'),
        ]);

        return redirect()->to('/usuarios')->with('message', 'Usuario creado exitosamente');
    }

    public function edit(int $id)
    {
        $user = $this->usuarios->find($id);
        $especialidades = $this->especialidades->findAll(); // Si necesitas especialidades

        if ($user['especialidad_id']) {
            $user['especialidad'] = $this->especialidades->find($user['especialidad_id']);
        } else {
            $user['especialidad'] = null; // Si no tiene especialidad
        }
        $roles = $this->roles->findAll();
        return view('usuarios/edit', compact('user', 'roles', 'especialidades'));
    }

    public function update(int $id)
    {
        $rules = [
            'nombre' => 'required|min_length[3]',
            'email' => "required|valid_email|is_unique[usuarios.email,id,{$id}]",
            'telefono' => 'permit_empty|regex_match[/^[0-9\+\-\s]+$/]',
            'role_id' => 'required|is_not_unique[roles.id]'
        ];
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'nombre' => $this->request->getPost('nombre'),
            'email' => $this->request->getPost('email'),
            'telefono' => $this->request->getPost('telefono'),
            'role_id' => $this->request->getPost('role_id'),
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

    public function perfil(int $id){
        $user = $this->usuarios
            ->select('usuarios.*, roles.nombre AS rol, especialidades.nombre AS especialidad')
            ->join('roles', 'roles.id = usuarios.role_id')
            ->join('especialidades', 'especialidades.id = usuarios.especialidad_id', 'left') // LEFT JOIN por si hay usuarios sin especialidad
            ->find($id);

        if (!$user) {
            return redirect()->to('/usuarios')->with('error', 'Usuario no encontrado');
        }

        return view('usuarios/perfil', compact('user'));
    }

    public function seedDemo()
    {
        $this->usuarios->insertBatch([
            [
                'nombre' => 'Admin Demo',
                'email' => 'admin@demo.com',
                'telefono' => '555-0001',
                'password_hash' => password_hash('admin123', PASSWORD_DEFAULT),
                'role_id' => 1,
            ],
            [
                'nombre' => 'Médico Demo',
                'email' => 'medico@demo.com',
                'telefono' => '555-0002',
                'password_hash' => password_hash('medico123', PASSWORD_DEFAULT),
                'role_id' => 2,
            ],
            [
                'nombre' => 'Recepcion Demo',
                'email' => 'recepcion@demo.com',
                'telefono' => '555-0003',
                'password_hash' => password_hash('recep123', PASSWORD_DEFAULT),
                'role_id' => 3,
            ],
        ]);

        return 'Usuarios de prueba creados';
    }
}

