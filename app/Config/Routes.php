<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Login
$routes->get('/', 'Usuarios::login');
$routes->get('login', 'Usuarios::login');
$routes->post('login', 'Usuarios::login');

// Ruta de desarrollo para poblar base de datos demo
$routes->get('dev/seed', 'Usuarios::seedDemo');


// ==============================
// Usuarios (Solo Administración)
// ==============================
$routes->group('usuarios', ['filter' => 'srole:1'], function ($routes) {
    $routes->get('', 'Usuarios::index');                 // Listado
    $routes->get('new', 'Usuarios::create');             // Formulario nuevo
    $routes->post('', 'Usuarios::store');                // Guardar nuevo
    $routes->get('(:num)/edit', 'Usuarios::edit/$1');    // Editar
    $routes->put('(:num)', 'Usuarios::update/$1');       // Actualizar (PUT)
    $routes->patch('(:num)', 'Usuarios::update/$1');     // Actualizar (PATCH)
    $routes->delete('(:num)', 'Usuarios::delete/$1');    // Eliminar
});


// ==============================
// Especialidades (Solo Administración)
// ==============================
$routes->group('especialidades', ['filter' => 'srole:1'], function ($routes) {
    $routes->get('', 'Especialidades::index');              // Listado
    $routes->get('new', 'Especialidades::create');          // Formulario nuevo
    $routes->post('', 'Especialidades::store');             // Guardar nuevo
    $routes->get('(:num)/edit', 'Especialidades::edit/$1'); // Editar
    $routes->put('(:num)', 'Especialidades::update/$1');    // Actualizar (PUT)
    $routes->patch('(:num)', 'Especialidades::update/$1');  // Actualizar (PATCH)
    $routes->delete('(:num)', 'Especialidades::delete/$1'); // Eliminar
});


// ==============================
// Horarios (Administración y Médicos)
// ==============================
$routes->group('horarios', ['filter' => 'srole:1,2'], function ($routes) {
    $routes->get('(:num)/medico', 'Horarios::medico/$1'); // Horario de un médico específico
    $routes->get('(:num)/new', 'Horarios::create/$1');             // Formulario nuevo 
    $routes->post('', 'Horarios::store');                // Guardar nuevo
    $routes->get('(:num)/edit', 'Horarios::edit/$1');    // Editar
    $routes->put('(:num)', 'Horarios::update/$1');       // Actualizar (PUT)
    $routes->patch('(:num)', 'Horarios::update/$1');     // Actualizar (PATCH)
    $routes->delete('(:num)', 'Horarios::delete/$1');    // Eliminar
});

$routes->group('horarios', ['filter' => 'srole:1,3'], function ($routes) {
  $routes->get('', 'Horarios::index');              // Listado
});

$routes->get('api/disponibilidad/(:num)/(:segment)', 'Horarios::disponibilidadPorFecha/$1/$2');
// $1 = ID del médico, $2 = fecha (ej: 2025-09-02)



// ==============================
// Consultas (Administración y Médicos)
// ==============================
$routes->group('consultas', ['filter' => 'srole:1,2'], function ($routes) {
    $routes->get('(:num)/detalles', 'Consultas::detalles/$1');
    $routes->get('(:num)/edit', 'Consultas::edit/$1');    // Editar
    $routes->put('(:num)', 'Consultas::update/$1');       // Actualizar (PUT)
    $routes->patch('(:num)', 'Consultas::update/$1');     // Actualizar (PATCH)
    $routes->delete('(:num)', 'Consultas::delete/$1');    // Eliminar
});
$routes->group('consultas', ['filter' => 'srole:1,2,3'], function ($routes) {
    $routes->get('', 'Consultas::index');               // Listado
    $routes->get('(:num)/detalles', 'Consultas::detalles/$1');
    $routes->get('(:num)/new', 'Consultas::create/$1');             // Formulario nuevo 
    $routes->post('', 'Consultas::store');                // Guardar nuevo
});


// ==============================
// Pacientes (Administración y Recepcionista)
// ==============================
$routes->group('pacientes', ['filter' => 'srole:1'], function ($routes) {
    $routes->delete('(:num)', 'Pacientes::delete/$1');  // Eliminar
});

$routes->group('pacientes', ['filter' => 'srole:1,3'], function ($routes) {
    $routes->get('', 'Pacientes::index');               // Listado
    $routes->get('new', 'Pacientes::create');           // Formulario nuevo
    $routes->post('', 'Pacientes::store');              // Guardar nuevo
    $routes->get('(:num)/edit', 'Pacientes::edit/$1');  // Editar
    $routes->put('(:num)', 'Pacientes::update/$1');     // Actualizar (PUT)
    $routes->patch('(:num)', 'Pacientes::update/$1');   // Actualizar (PATCH)
});

// ==============================
// Expedientes (Administración y Médico)
// ==============================
$routes->group('expedientes', ['filter' => 'srole:1,2'], function ($routes) {
    $routes->get('(:num)/new', 'Expedientes::create/$1');                 // Formulario nuevo
    $routes->post('', 'Expedientes::store');                     // Guardar nuevo
    $routes->get('(:num)/edit', 'Expedientes::edit/$1');         // Editar
    $routes->put('(:num)', 'Expedientes::update/$1');            // Actualizar (PUT)
    $routes->patch('(:num)', 'Expedientes::update/$1');          // Actualizar (PATCH)
    $routes->delete('(:num)', 'Expedientes::delete/$1');         // Eliminar
    $routes->get('(:num)/detalles', 'Expedientes::detalles/$1'); // Ver detalles de un expediente
    $routes->get('(:num)/medico', 'Expedientes::medico/$1'); // Ver expediente de un médico específico
});

$routes->group('expedientes', ['filter' => 'srole:1,2,3'], function ($routes) {
    $routes->get('(:num)/paciente', 'Expedientes::paciente/$1'); // Ver expediente de un médico específico
});



// Vistas separadas por rol
$routes->get('expedientes', 'Expedientes::index', ['filter' => 'srole:1']);

// ==============================
// Dashboards por Rol
// ==============================
$routes->get('admin/dashboard', 'Usuarios::admin', ['filter' => 'srole:1']);
$routes->get('medico/dashboard', 'Usuarios::medico', ['filter' => 'srole:2']);
$routes->get('recepcionista/dashboard', 'Usuarios::recepcionista', ['filter' => 'srole:3']);
