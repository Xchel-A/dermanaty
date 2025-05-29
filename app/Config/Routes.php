<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Usuarios::login');
$routes->get('login', 'Usuarios::login');
$routes->post('login', 'Usuarios::login');

$routes->get('dev/seed', 'Usuarios::seedDemo');

// CRUD solo rol 1 (Administracion)
$routes->group('usuarios', ['filter' => 'srole:1'], function ($routes) {
    $routes->get('',             'Usuarios::index');
    $routes->get('new',          'Usuarios::create');
    $routes->post('',            'Usuarios::store');
    $routes->get('(:num)/edit',  'Usuarios::edit/$1');
    $routes->put('(:num)',       'Usuarios::update/$1');
    $routes->patch('(:num)',     'Usuarios::update/$1');
    $routes->delete('(:num)',    'Usuarios::delete/$1');
});

$routes->group('usuarios', ['filter' => 'srole:1'], function ($routes) {
    $routes->get('',             'Usuarios::index');
    $routes->get('new',          'Usuarios::create');
    $routes->post('',            'Usuarios::store');
    $routes->get('(:num)/edit',  'Usuarios::edit/$1');
    $routes->put('(:num)',       'Usuarios::update/$1');
    $routes->patch('(:num)',     'Usuarios::update/$1');
    $routes->delete('(:num)',    'Usuarios::delete/$1');
});

// CRUD  administracion y recepcionista (roles 1 y 3)
$routes->group('pacientes', ['filter' => 'srole:1,3'], function ($routes) {
    $routes->get('',             'Pacientes::index');
    $routes->get('new',          'Pacientes::create');
    $routes->post('',            'Pacientes::store');
    $routes->get('(:num)/edit',  'Pacientes::edit/$1');
    $routes->put('(:num)',       'Pacientes::update/$1');
    $routes->patch('(:num)',     'Pacientes::update/$1');
    $routes->delete('(:num)',    'Pacientes::delete/$1');
});


// Rutas para crear, editar, eliminar accesibles para admin y médico (roles 1 y 3)
$routes->group('expedientes', ['filter' => 'srole:1,3'], function ($routes) {
    $routes->get('new',          'Expedientes::create');
    $routes->post('',            'Expedientes::store');
    $routes->get('(:num)/edit',  'Expedientes::edit/$1');
    $routes->put('(:num)',       'Expedientes::update/$1');
    $routes->patch('(:num)',     'Expedientes::update/$1');
    $routes->delete('(:num)',    'Expedientes::delete/$1');
    $routes->get('(:num)/detalles', 'Expedientes::detalles/$1');
});


// Ruta para ver expedientes — según rol, llamar al método correspondiente
$routes->get('expedientes', 'Expedientes::index' , ['filter' => 'srole:1']);
$routes->get('expedientes/misexpedientes', 'Expedientes::misExpedientes', ['filter' => 'srole:3']);



// Dashboards
$routes->get('admin/dashboard',         'Usuarios::admin',         ['filter' => 'srole:1']);
$routes->get('medico/dashboard',        'Usuarios::medico',        ['filter' => 'srole:2']);
$routes->get('recepcionista/dashboard', 'Usuarios::recepcionista', ['filter' => 'srole:3']);
