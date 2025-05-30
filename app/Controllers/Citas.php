<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CitasM;
use App\Models\UsuariosM;

/**
 * Controlador de citas: CRUD
 * - CRUD completo de citas
 */
class Citas extends BaseController
{
    protected CitasM $Citas;

    protected UsuariosM $usuarios;
    protected $session;

    public function __construct()
    {
        $this->citas = new CitasM();
        $this->usuarios = new UsuariosM();
        $this->session = session();
    }



    /*=================================================
    |  CRUD Citas (Administracion y Recepcionista)            |
    =================================================*/
    public function index()
    {
        $medicos = $this->usuarios->where('role_id', 2)->findAll(); // Obtener médicos

        return view('citas/index', compact('medicos'));
    }

    public function create()
    {
        return view('citas/create');
    }

    public function store()
    {
        $rules = [
            'paciente_id' => 'required|is_natural_no_zero',
            'medico_id' => 'required|is_natural_no_zero',
            'fecha_inicio' => 'required|valid_date',
            'fecha_fin' => 'required|valid_date',
            'estado' => 'permit_empty|in_list[Pendiente,Confirmada,Cancelada,Realizada]',
            'costo' => 'required|decimal',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'paciente_id' => $this->request->getPost('paciente_id'),
            'medico_id' => $this->request->getPost('medico_id'),
            'fecha_inicio' => $this->request->getPost('fecha_inicio'),
            'fecha_fin' => $this->request->getPost('fecha_fin'),
            'estado' => $this->request->getPost('estado') ?? 'Pendiente',
            'creado_por' => session()->get('user_id'), // Asegúrate que este dato esté en sesión
            'creado_at' => date('Y-m-d H:i:s'),
            'costo' => $this->request->getPost('costo'),
        ];

        $idInsertado = $this->citas->insert($data, true);

        registrarAuditoria('INSERT', 'citas', $idInsertado, null, $data);

        return redirect()->to('/citas')->with('message', 'Cita creada exitosamente.');
    }


    public function edit(int $id)
    {
        $especialidad = $this->citas->find($id);
        return view('citas/edit', compact('especialidad'));
    }

    public function update(int $id)
    {
        $rules = [
            'fecha_inicio' => 'required|valid_date',
            'fecha_fin' => 'required|valid_date',
            'estado' => 'permit_empty|in_list[Pendiente,Confirmada,Cancelada,Realizada]',
            'costo' => 'required|decimal',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $datosAntiguos = $this->citas->find($id);

        $data = [
            'fecha_inicio' => $this->request->getPost('fecha_inicio'),
            'fecha_fin' => $this->request->getPost('fecha_fin'),
            'estado' => $this->request->getPost('estado'),
            'costo' => $this->request->getPost('costo'),
        ];

        $this->citas->update($id, $data);

        registrarAuditoria('UPDATE', 'citas', $id, $datosAntiguos, $data);

        return redirect()->to('/citas')->with('message', 'Cita actualizada exitosamente.');
    }



    public function delete(int $id)
    {
        $datosAntiguos = $this->citas->find($id);

        $this->citas->delete($id);

        registrarAuditoria('DELETE', 'citas', $id, $datosAntiguos, null);
        return redirect()->to('/citas')->with('message', 'Usuario eliminado');
    }

    public function medico(int $medicoId)
    {
        $citas = $this->citas->where('medico_id', $medicoId)->findAll();
        return view('citas/medico', compact('citas', 'medicoId'));
    }
    
}

