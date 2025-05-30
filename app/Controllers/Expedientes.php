<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CitasM;
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

    protected CitasM $citas;
    protected $session;

    public function __construct()
    {
        $this->expedientes = new ExpedientesM();
        $this->pacientes = new PacientesM();
        $this->Usuarios = new UsuariosM();
        $this->citas = new CitasM();
        $this->consultas = new ConsultasM();
        $this->session = session();
    }

    /*=================================================
    |  CRUD Expedientes (Administracion y Recepcionista)            |
    =================================================*/

    public function create($id)
    {

        // Obtener pacientes que tengan al menos una cita con este médico
        $pacientes = $this->citas
            ->select('paciente_id')
            ->where('medico_id', $id)
            ->groupBy('paciente_id')
            ->asArray()
            ->findAll();

        $medicoId = $id;
        // Convertir a array plano de IDs de pacientes
        $pacientes = array_column($pacientes, 'paciente_id');

        $pacientes = $this->pacientes
            ->whereIn('id', $pacientes)
            ->findAll();

        return view('expedientes/create', compact('pacientes', 'medicoId'));
    }

    public function store()
    {
        $rules = [
            'paciente_id' => 'required|is_natural_no_zero',
            'motivo_apertura' => 'permit_empty|max_length[255]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Preparar datos
        $data = [
            'paciente_id' => $this->request->getPost('paciente_id'),
            'creado_por' => session()->get('id'), // Asumiendo que tienes auth con el ID del usuario en sesión
            'fecha_apertura' => date('Y-m-d H:i:s'),
            'motivo_apertura' => htmlspecialchars($this->request->getPost('motivo_apertura')),
            'estado' => 'Activo'
        ];

        $this->Expedientes->insert($data);

        return redirect()->to('/expedientes')->with('message', 'Expediente creado correctamente.');
    }

    public function edit(int $id)
    {
        $paciente = $this->Expedientes->find($id);
        return view('Expedientes/edit', compact('paciente'));
    }

    public function update(int $id)
    {
        $rules = [
            'motivo_apertura' => 'permit_empty|max_length[255]',
            'estado' => 'required|in_list[Activo,Cerrado]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'motivo_apertura' => htmlspecialchars($this->request->getPost('motivo_apertura')),
            'estado' => $this->request->getPost('estado')
        ];

        $this->Expedientes->update($id, $data);

        return redirect()->to('/expedientes')->with('message', 'Expediente actualizado correctamente.');
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
        if (!$expediente) {
            return redirect()->to('/expedientes')->with('error', 'Expediente no encontrado.');
        }

        $consultas = $this->consultas->where('expediente_id', $id)->findAll();


        // Mostrar vista con datos del expediente
        return view('expedientes/detalles', compact('expediente', 'consultas'));
    }

    public function medico(int $id)
    {
        // Obtener expedientes del médico
        $expedientes = $this->expedientes
            ->select('expedientes.*, pacientes.nombre AS paciente_nombre, pacientes.apellidos AS paciente_apellidos')
            ->join('pacientes', 'pacientes.id = expedientes.paciente_id')
            ->where('expedientes.creado_por', $id)
            ->asArray()
            ->findAll();

        $medicoId = $id;

        // Devolver a la vista con compact
        return view('expedientes/medico', compact('expedientes', 'medicoId'));
    }


}

