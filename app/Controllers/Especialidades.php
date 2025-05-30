<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\EspecialidadesM;

/**
 * Controlador de especialidades: CRUD
 * - CRUD completo de especialidades
 */
class Especialidades extends BaseController
{
    protected EspecialidadesM $Especialidades;
    protected $session;

    public function __construct()
    {
        $this->especialidades = new EspecialidadesM();
        $this->session = session();
    }

    /*=================================================
    |  CRUD Especialidades (Administracion y Recepcionista)            |
    =================================================*/
    public function index()
    {
        $especialidades = $this->especialidades->findAll();

        return view('especialidades/index', compact('especialidades'));
    }

    public function create()
    {
        return view('especialidades/create');
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

        $idInsertado = $this->especialidades->insert($data, true); // true = obtener ID insertado

        registrarAuditoria('INSERT', 'especialidades', $idInsertado, null, $data);

        return redirect()->to('/especialidades')->with('message', 'Especialidad creado exitosamente');
    }


    public function edit(int $id)
    {
        $especialidad = $this->especialidades->find($id);
        return view('especialidades/edit', compact('especialidad'));
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


        $datosAntiguos = $this->especialidades->find($id);

        // Sanitizar entrada
        $data = [
            'nombre' => htmlspecialchars($this->request->getPost('nombre')),
            'activo' => $this->request->getPost('activado') ? 1 : 0,
        ];

        $this->especialidades->update($id, $data);

        registrarAuditoria('UPDATE', 'especialidades', $id, $datosAntiguos, $data);

        return redirect()->to('/especialidades')->with('message', 'Paciente actualizado exitosamente');
    }


    public function delete(int $id)
    {
        $datosAntiguos = $this->especialidades->find($id);

        $this->especialidades->delete($id);
        
        registrarAuditoria('DELETE', 'especialidades', $id, $datosAntiguos, null);
        return redirect()->to('/especialidades')->with('message', 'Usuario eliminado');
    }


}

