<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ExpedientesM;
use App\Models\PacientesM;

/**
 * Controlador de pacientes: CRUD
 * - CRUD completo de pacientes
 */
class Pacientes extends BaseController
{
    protected PacientesM $Pacientes;

    protected ExpedientesM $expedientes;
    protected $session;

    public function __construct()
    {
        $this->pacientes = new PacientesM();
        $this->expedientes = new ExpedientesM();
        $this->session = session();
    }



    /*=================================================
    |  CRUD Pacientes (Administracion y Recepcionista)            |
    =================================================*/
    public function index()
    {
        $pacientes = $this->pacientes->findAll();

        return view('pacientes/index', compact('pacientes'));
    }

    public function create()
    {
        return view('pacientes/create');
    }

    public function store()
    {
        $rules = [
            'nombre' => 'required|min_length[3]|max_length[100]',
            'apellidos' => 'required|min_length[3]|max_length[100]',
            'fecha_nacimiento' => 'required|valid_date',
            'genero' => 'required|in_list[M,F,O]',
            'telefono' => 'permit_empty|regex_match[/^\+?[0-9\s\-]{7,15}$/]',
            'email' => 'required|valid_email|is_unique[Pacientes.email]|max_length[150]',
            'direccion' => 'permit_empty|min_length[5]|max_length[255]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Validar que la fecha no sea futura
        $fechaNacimiento = $this->request->getPost('fecha_nacimiento');
        if (strtotime($fechaNacimiento) > time()) {
            return redirect()->back()->withInput()->with('errors', 'La fecha de nacimiento no puede ser en el futuro.');
        }

        // Sanitizar entrada
        $data = [
            'nombre' => htmlspecialchars($this->request->getPost('nombre')),
            'apellidos' => htmlspecialchars($this->request->getPost('apellidos')),
            'fecha_nacimiento' => $fechaNacimiento,
            'genero' => $this->request->getPost('genero'),
            'telefono' => $this->request->getPost('telefono'),
            'email' => filter_var($this->request->getPost('email'), FILTER_SANITIZE_EMAIL),
            'direccion' => htmlspecialchars($this->request->getPost('direccion')),
        ];

        $idInsertado = $this->pacientes->insert($data, true); // true = obtener ID insertado

        registrarAuditoria('INSERT', 'pacientes', $idInsertado, null, $data);

        return redirect()->to('/pacientes')->with('message', 'Paciente creado exitosamente');
    }


    public function edit(int $id)
    {
        $paciente = $this->pacientes->find($id);
        $expedientes = $this->expedientes->where('paciente_id', $id)->findAll();
        return view('pacientes/edit', compact('paciente', 'expedientes'));
    }

    public function update(int $id)
    {
        $rules = [
            'nombre' => 'required|min_length[3]|max_length[100]',
            'apellidos' => 'required|min_length[3]|max_length[100]',
            'fecha_nacimiento' => 'required|valid_date',
            'genero' => 'required|in_list[M,F,O]',
            'telefono' => 'permit_empty|regex_match[/^\+?[0-9\s\-]{7,15}$/]',
            'email' => "required|valid_email|is_unique[Pacientes.email,id,{$id}]|max_length[150]",
            'direccion' => 'permit_empty|min_length[5]|max_length[255]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Validar que la fecha no sea futura
        $fechaNacimiento = $this->request->getPost('fecha_nacimiento');
        if (strtotime($fechaNacimiento) > time()) {
            return redirect()->back()->withInput()->with('errors', 'La fecha de nacimiento no puede ser en el futuro.');
        }

        $datosAntiguos = $this->pacientes->find($id);

        // Sanitizar entrada
        $data = [
            'nombre' => htmlspecialchars($this->request->getPost('nombre')),
            'apellidos' => htmlspecialchars($this->request->getPost('apellidos')),
            'fecha_nacimiento' => $fechaNacimiento,
            'genero' => $this->request->getPost('genero'),
            'telefono' => $this->request->getPost('telefono'),
            'email' => filter_var($this->request->getPost('email'), FILTER_SANITIZE_EMAIL),
            'direccion' => htmlspecialchars($this->request->getPost('direccion')),
        ];

        $this->pacientes->update($id, $data);

        registrarAuditoria('UPDATE', 'pacientes', $id, $datosAntiguos, $data);

        return redirect()->to('/pacientes')->with('message', 'Paciente actualizado exitosamente');
    }


    public function delete(int $id)
    {
        $datosAntiguos = $this->pacientes->find($id);

        $this->pacientes->delete($id);
        
        registrarAuditoria('DELETE', 'pacientes', $id, $datosAntiguos, null);
        return redirect()->to('/pacientes')->with('message', 'Usuario eliminado');
    }


}

