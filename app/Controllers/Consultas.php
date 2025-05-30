<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ConsultasM;
use App\Models\EspecialidadesM;
use App\Models\ExpedientesM;
use App\Models\PacientesM;
use App\Models\UsuariosM;

/**
 * Controlador de consultas: CRUD
 * - CRUD completo de consultas
 */
class Consultas extends BaseController
{
    protected ConsultasM $Consultas;

    protected PacientesM $pacientes;

    protected ExpedientesM $expedientes;

    protected EspecialidadesM $especialidades;
    

    protected UsuariosM $usuarios;
    protected $session;

    public function __construct()
    {
        $this->consultas = new ConsultasM();
        $this->usuarios = new UsuariosM();
        $this->pacientes = new PacientesM();
        $this->expedientes = new ExpedientesM();
        $this->especialidades = new EspecialidadesM();
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

public function detalles(int $id)
{
    $consulta = $this->consultas->find($id);
    if (!$consulta) {
        return redirect()->to('/consultas')->with('error', 'Consulta no encontrada.');
    }

    // Obtener datos del médico
    $medico = $this->usuarios->find($consulta['medico_id']);
    $especialidad = null;

    if ($medico && isset($medico['especialidad_id'])) {
        $especialidad = $this->especialidades->find($medico['especialidad_id']);
    }

    // Obtener expediente para llegar al paciente
    $expediente = $this->expedientes->find($consulta['expediente_id']);
    $paciente = null;

    if ($expediente && isset($expediente['paciente_id'])) {
        $paciente = $this->pacientes->find($expediente['paciente_id']);
    }

    return view('consultas/detalles', compact('consulta', 'medico', 'especialidad', 'paciente'));
}



    public function create()
    {
        return view('consultas/create');
    }

    public function store()
    {
        $rules = [
            'expediente_id' => 'required|is_natural_no_zero',
            'medico_id' => 'required|is_natural_no_zero',
            'fecha_consulta' => 'required|valid_date',
            'motivo' => 'permit_empty',
            'diagnostico' => 'permit_empty',
            'tratamiento' => 'permit_empty',
            'notas' => 'permit_empty',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'expediente_id' => $this->request->getPost('expediente_id'),
            'medico_id' => $this->request->getPost('medico_id'),
            'fecha_consulta' => $this->request->getPost('fecha_consulta'),
            'motivo' => htmlspecialchars($this->request->getPost('motivo')),
            'diagnostico' => htmlspecialchars($this->request->getPost('diagnostico')),
            'tratamiento' => htmlspecialchars($this->request->getPost('tratamiento')),
            'notas' => htmlspecialchars($this->request->getPost('notas'))
        ];

        $idInsertado = $this->consultas->insert($data, true);

        registrarAuditoria('INSERT', 'consultas', $idInsertado, null, $data);

        return redirect()->to('/consultas')->with('message', 'Consulta creada exitosamente.');
    }



    public function edit(int $id)
    {
        $especialidad = $this->consultas->find($id);
        return view('consultas/edit', compact('especialidad'));
    }

    public function update(int $id)
    {
        $rules = [
            'fecha_consulta' => 'required|valid_date',
            'motivo' => 'permit_empty',
            'diagnostico' => 'permit_empty',
            'tratamiento' => 'permit_empty',
            'notas' => 'permit_empty',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $datosAntiguos = $this->consultas->find($id);

        $data = [
            'fecha_consulta' => $this->request->getPost('fecha_consulta'),
            'motivo' => htmlspecialchars($this->request->getPost('motivo')),
            'diagnostico' => htmlspecialchars($this->request->getPost('diagnostico')),
            'tratamiento' => htmlspecialchars($this->request->getPost('tratamiento')),
            'notas' => htmlspecialchars($this->request->getPost('notas')),
        ];

        $this->consultas->update($id, $data);

        registrarAuditoria('UPDATE', 'consultas', $id, $datosAntiguos, $data);

        return redirect()->to('/consultas')->with('message', 'Consulta actualizada exitosamente.');
    }



    public function delete(int $id)
    {
        $datosAntiguos = $this->consultas->find($id);

        $this->consultas->delete($id);

        registrarAuditoria('DELETE', 'consultas', $id, $datosAntiguos, null);
        return redirect()->to('/consultas')->with('message', 'Usuario eliminado');
    }


}

