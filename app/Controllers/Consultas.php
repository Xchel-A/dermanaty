<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ConsultasM;
use App\Models\UsuariosM;

/**
 * Controlador de consultas: CRUD
 * - CRUD completo de consultas
 */
class Consultas extends BaseController
{
    protected ConsultasM $Consultas;

    protected UsuariosM $usuarios;
    protected $session;

    public function __construct()
    {
        $this->consultas = new ConsultasM();
        $this->usuarios = new UsuariosM();
        $this->session = session();
    }



    /*=================================================
    |  CRUD Consultas (Administracion y Recepcionista)            |
    =================================================*/
    public function index()
    {
        $consultas = $this->consultas->findAll();
        $medicos = $this->usuarios->where('rol_id', 2)->findAll(); // Obtener médicos

        return view('consultas/index', compact('consultas', 'medicos'));
    }

    public function create()
    {
        return view('consultas/create');
    }

    public function store()
    {
        $rules = [
            'nombre' => 'required|min_length[3]|max_length[100]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Sanitizar entrada
        $data = [
            'nombre' => htmlspecialchars($this->request->getPost('nombre')),
        ];

        $idInsertado = $this->consultas->insert($data, true); // true = obtener ID insertado

        registrarAuditoria('INSERT', 'consultas', $idInsertado, null, $data);

        return redirect()->to('/consultas')->with('message', 'Especialidad creado exitosamente');
    }


    public function edit(int $id)
    {
        $especialidad = $this->consultas->find($id);
        return view('consultas/edit', compact('especialidad'));
    }

    public function update(int $id)
    {
        $rules = [
            'nombre' => 'required|min_length[3]|max_length[100]',
            'activado' => 'permit_empty|in_list[1,0]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }


        $datosAntiguos = $this->consultas->find($id);

        // Sanitizar entrada
        $data = [
            'nombre' => htmlspecialchars($this->request->getPost('nombre')),
            'activo' => $this->request->getPost('activado') ? 1 : 0,
        ];

        $this->consultas->update($id, $data);

        registrarAuditoria('UPDATE', 'consultas', $id, $datosAntiguos, $data);

        return redirect()->to('/consultas')->with('message', 'Paciente actualizado exitosamente');
    }


    public function delete(int $id)
    {
        $datosAntiguos = $this->consultas->find($id);

        $this->consultas->delete($id);
        
        registrarAuditoria('DELETE', 'consultas', $id, $datosAntiguos, null);
        return redirect()->to('/consultas')->with('message', 'Usuario eliminado');
    }


}

