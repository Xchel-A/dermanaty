<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ConsultasM;
use App\Models\ExpedientesM;
use App\Models\PacientesM;
use App\Models\UsuariosM;

/**
 * Controlador de Expedientes: login + CRUD
 * - Redirección de vistas según rol (admin, médico, recepcionista)
 * - CRUD completo de Expedientes (solo accesible para rol Administracion)
 */
class Expedientes extends BaseController
{
    protected ExpedientesM $Expedientes;

    protected PacientesM $pacientes;
    
    protected UsuariosM $Usuarios;

    protected ConsultasM $consultas;
    protected $session;

    public function __construct()
    {
        $this->expedientes = new ExpedientesM();
        $this->pacientes  = new PacientesM();
        $this->Usuarios   = new UsuariosM();
        $this->consultas  = new ConsultasM();
        $this->session  = session();
    }



    /*=================================================
    |  CRUD Expedientes (Administracion y Recepcionista)            |
    =================================================*/
    public function index()
    {
        $expedientes = $this->expedientes->findAll();

        return view('Expedientes/index', compact('expedientes'));
    }

    public function create()
    {
        return view('Expedientes/create');
    }

    public function store()
    {
        $rules = [
            'nombre'           => 'required|min_length[3]|max_length[100]',
            'apellidos'        => 'required|min_length[3]|max_length[100]',
            'fecha_nacimiento' => 'required|valid_date',
            'genero'           => 'required|in_list[M,F,O]',
            'telefono'         => 'permit_empty|regex_match[/^\+?[0-9\s\-]{7,15}$/]',
            'email'            => 'required|valid_email|is_unique[Expedientes.email]|max_length[150]',
            'direccion'        => 'permit_empty|min_length[5]|max_length[255]',
        ];
    
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
    
        // Validar que la fecha no sea futura
        $fechaNacimiento = $this->request->getPost('fecha_nacimiento');
        if (strtotime($fechaNacimiento) > time()) {
            return redirect()->back()->withInput()->with('errors', 'La fecha de nacimiento no puede ser en el futuro.');
        }
    
        // Sanitizar entrada
        $data = [
            'nombre'           => htmlspecialchars($this->request->getPost('nombre')),
            'apellidos'        => htmlspecialchars($this->request->getPost('apellidos')),
            'fecha_nacimiento' => $fechaNacimiento,
            'genero'           => $this->request->getPost('genero'),
            'telefono'         => $this->request->getPost('telefono'),
            'email'            => filter_var($this->request->getPost('email'), FILTER_SANITIZE_EMAIL),
            'direccion'        => htmlspecialchars($this->request->getPost('direccion')),
        ];
    
        $this->Expedientes->insert($data);
    
        return redirect()->to('/Expedientes')->with('message', 'Paciente creado exitosamente');
    }
    

    public function edit(int $id)
    {
        $paciente  = $this->Expedientes->find($id);
        return view('Expedientes/edit', compact('paciente'));
    }

    public function update(int $id)
    {
        $rules = [
            'nombre'           => 'required|min_length[3]|max_length[100]',
            'apellidos'        => 'required|min_length[3]|max_length[100]',
            'fecha_nacimiento' => 'required|valid_date',
            'genero'           => 'required|in_list[M,F,O]',
            'telefono'         => 'permit_empty|regex_match[/^\+?[0-9\s\-]{7,15}$/]',
            'email'            => "required|valid_email|is_unique[Expedientes.email,id,{$id}]|max_length[150]",
            'direccion'        => 'permit_empty|min_length[5]|max_length[255]',
        ];
    
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
    
        // Validar que la fecha no sea futura
        $fechaNacimiento = $this->request->getPost('fecha_nacimiento');
        if (strtotime($fechaNacimiento) > time()) {
            return redirect()->back()->withInput()->with('errors', 'La fecha de nacimiento no puede ser en el futuro.');
        }
    
        // Sanitizar entrada
        $data = [
            'nombre'           => htmlspecialchars($this->request->getPost('nombre')),
            'apellidos'        => htmlspecialchars($this->request->getPost('apellidos')),
            'fecha_nacimiento' => $fechaNacimiento,
            'genero'           => $this->request->getPost('genero'),
            'telefono'         => $this->request->getPost('telefono'),
            'email'            => filter_var($this->request->getPost('email'), FILTER_SANITIZE_EMAIL),
            'direccion'        => htmlspecialchars($this->request->getPost('direccion')),
        ];

        $this->Expedientes->update($id, $data);
    
        return redirect()->to('/Expedientes')->with('message', 'Paciente actualizado exitosamente');
    }
    

    public function delete(int $id)
    {
        $this->expedientes->delete($id);
        return redirect()->to('/expedientes')->with('message', 'Expediente eliminado');
    }


    public function detalles(int $id)
    {
        // Obtener expediente con datos del paciente y médico mediante JOIN
        $expediente = $this->expedientes
        ->select('
            expedientes.*, 
            pacientes.nombre AS paciente_nombre, 
            pacientes.apellidos AS paciente_apellidos,
            usuarios.nombre AS medico_nombre, 
            usuarios.telefono AS medico_telefono
        ')
        ->join('pacientes', 'pacientes.id = expedientes.paciente_id')
        ->join('usuarios', 'usuarios.id = expedientes.creado_por')
        ->where('expedientes.id', $id)
        ->asArray()
        ->first();

        // Validación: si no existe el expediente, redirigir con error
        if (! $expediente) {
            return redirect()->to('/expedientes')->with('error', 'Expediente no encontrado.');
        }

        $consultas = $this->consultas->where('expediente_id', $id)->findAll();
    
    
        // Mostrar vista con datos del expediente
        return view('expedientes/detalles', compact('expediente', 'consultas'));
    }
    

}

