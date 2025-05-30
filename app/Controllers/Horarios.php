<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CitasM;
use App\Models\HorariosM;
use App\Models\UsuariosM;

/**
 * Controlador de horarios: CRUD
 * - CRUD completo de horarios
 */
class Horarios extends BaseController
{
    protected HorariosM $Horarios;

    protected UsuariosM $usuarios;

    protected CitasM $citas;
    protected $session;

    public function __construct()
    {
        $this->horarios = new HorariosM();
        $this->usuarios = new UsuariosM();
        $this->citas = new CitasM();
        $this->session = session();
    }



    /*=================================================
    |  CRUD Horarios (Administracion y Recepcionista)            |
    =================================================*/

    /**
     * Muestra los horarios de un médico específico.
     * Solo accesible por el propio médico o por un administrador.
     */
    public function index()
    {


        $medicos = $this->usuarios->where('role_id', 2)->findAll();
        $horarios = $this->horarios->findAll();

        return view('horarios/index', compact('medicos', 'horarios'));
    }

    public function disponibilidadPorFecha(int $medicoId, $fecha)
    {
        // Obtener el número del día de la semana según tu formato
        // Lunes = 1, Domingo = 7
        $diaSemana = date('N', strtotime($fecha));

        // Buscar el horario del médico para ese día
        $horario = $this->horarios
            ->where('medico_id', $medicoId)
            ->where('dia_semana', $diaSemana)
            ->first();

        if (!$horario) {
            return $this->response->setJSON([
                'disponibilidad' => [],
                'error' => 'El médico no tiene horario ese día.'
            ]);
        }

        $horaInicio = $horario['hora_inicio'];
        $horaFin = $horario['hora_fin'];

        // Buscar citas para ese médico en ese día
        $citas = $this->citas
            ->where('medico_id', $medicoId)
            ->where('estado', 'confirmada')
            ->where("DATE(fecha_inicio)", $fecha)
            ->orderBy('fecha_inicio', 'ASC')
            ->findAll();

        $disponibles = [];
        $inicioActual = $horaInicio;

        foreach ($citas as $cita) {
            $citaInicio = substr($cita['fecha_inicio'], 11, 5);
            $citaFin = substr($cita['fecha_fin'], 11, 5);

            if ($inicioActual < $citaInicio) {
                $disponibles[] = "$inicioActual - $citaInicio";
            }

            $inicioActual = max($inicioActual, $citaFin);
        }

        if ($inicioActual < $horaFin) {
            $disponibles[] = "$inicioActual - $horaFin";
        }

        return $this->response->setJSON([
            'fecha' => $fecha,
            'disponibilidad' => $disponibles,
            'horario_completo' => "$horaInicio - $horaFin"
        ]);
    }


    public function medico(int $id)
    {
        $id = (int) $id;

        // Validación del ID
        if ($id <= 0) {
            return redirect()->to('/')->with('error', 'ID de médico inválido.');
        }

        // Verifica permisos: debe ser administrador o el propio médico
        if (!$this->tienePermisoMedico($id)) {
            return redirect()->to('/')->with('error', 'No tienes permiso para ver esta información.');
        }

        // Verifica que el usuario (médico) exista
        $user = $this->usuarios->find($id);
        if (!$user) {
            return redirect()->to('/')->with('error', 'Médico no encontrado.');
        }

        // Obtiene los horarios del médico
        $horarios = $this->horarios->where('medico_id', $id)->findAll();

        return view('horarios/medico', compact('horarios', 'user'));
    }

    public function create(int $id)
    {
        $id = (int) $id;


        if (!$this->tienePermisoMedico($id)) {
            return redirect()->to('/')->with('error', 'No tienes permiso para realizar esta acción.');
        }

        $user = $this->usuarios->find($id);
        if (!$user || $user['role_id'] != 2) {
            // Si el usuario no es médico o no existe, redirige con error
            return redirect()->to('/')->with('error', 'Medico no encontrado.');
        }

        return view('horarios/create', compact('user'));
    }

    public function store()
    {
        $medico_id = (int) $this->request->getPost('medico_id');

        if (!$this->tienePermisoMedico($medico_id)) {
            return redirect()->to('/')->with('error', 'No tienes permiso para realizar esta acción.');
        }

        $rules = [
            'medico_id' => 'required|integer',
            'dia_semana' => 'required|in_list[Lunes,Martes,Miércoles,Jueves,Viernes,Sábado,Domingo]',
            'hora_inicio' => 'required|valid_time',
            'hora_fin' => 'required|valid_time',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'medico_id' => $medico_id,
            'dia_semana' => htmlspecialchars($this->request->getPost('dia_semana')),
            'hora_inicio' => $this->request->getPost('hora_inicio'),
            'hora_fin' => $this->request->getPost('hora_fin'),
        ];

        $idInsertado = $this->horarios->insert($data, true);
        registrarAuditoria('INSERT', 'horarios', $idInsertado, null, $data);

        return redirect()->to("/horarios/$medico_id/medico")->with('message', 'Horario creado exitosamente');

    }


    public function edit(int $id)
    {
        $horario = $this->horarios->find($id);
        if (!$horario) {
            return redirect()->to('/')->with('error', 'Horario no encontrado.');
        }

        if (!$this->tienePermisoMedico($horario['medico_id'])) {
            return redirect()->to('/')->with('error', 'No tienes permiso para editar este horario.');
        }

        return view('horarios/edit', compact('horario'));
    }

    public function update(int $id)
    {
        $horario = $this->horarios->find($id);
        if (!$horario) {
            return redirect()->to('/')->with('error', 'Horario no encontrado.');
        }

        if (!$this->tienePermisoMedico($horario['medico_id'])) {
            return redirect()->to('/')->with('error', 'No tienes permiso para modificar este horario.');
        }

        $rules = [
            'dia_semana' => 'required|in_list[Lunes,Martes,Miércoles,Jueves,Viernes,Sábado,Domingo]',
            'hora_inicio' => 'required|valid_time',
            'hora_fin' => 'required|valid_time'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $datosAntiguos = $horario;

        $data = [
            'dia_semana' => htmlspecialchars($this->request->getPost('dia_semana')),
            'hora_inicio' => $this->request->getPost('hora_inicio'),
            'hora_fin' => $this->request->getPost('hora_fin')
        ];

        $this->horarios->update($id, $data);
        registrarAuditoria('UPDATE', 'horarios', $id, $datosAntiguos, $data);
        return redirect()->to("/horarios/{$horario['medico_id']}/medico")->with('message', 'Horario actualizado exitosamente');

    }


    public function delete(int $id)
    {
        $horario = $this->horarios->find($id);
        if (!$horario) {
            return redirect()->to('/')->with('error', 'Horario no encontrado.');
        }

        if (!$this->tienePermisoMedico($horario['medico_id'])) {
            return redirect()->to('/')->with('error', 'No tienes permiso para eliminar este horario.');
        }

        registrarAuditoria('DELETE', 'horarios', $id, $horario, null);
        $this->horarios->delete($id);

        return redirect()->to("/horarios/medico/{$horario['medico_id']}")->with('message', 'Horario eliminado exitosamente');
    }


    private function tienePermisoMedico($medicoId): bool
    {
        $usuarioActualId = $this->session->get('user_id');
        $rol = $this->session->get('role_id');
        return $rol == 1 || $usuarioActualId == $medicoId;
    }

}

