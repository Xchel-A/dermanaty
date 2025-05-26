<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Usuarios::login');
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

// Dashboards
$routes->get('admin/dashboard',         'Usuarios::admin',         ['filter' => 'srole:1']);
$routes->get('medico/dashboard',        'Usuarios::medico',        ['filter' => 'srole:2']);
$routes->get('recepcionista/dashboard', 'Usuarios::recepcionista', ['filter' => 'srole:3']);
