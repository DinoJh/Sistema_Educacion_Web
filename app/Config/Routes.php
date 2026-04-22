<?php

namespace Config;

$routes = Services::routes();

if (is_file(SYSTEMPATH . 'Config/Routes.php')) {
    require SYSTEMPATH . 'Config/Routes.php';
}

$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(true);

$routes->get('/',          'Login::index');
$routes->get('/login',     'Login::index');
$routes->post('/logearse', 'Login::verificar');

$routes->get('/application',    'Application::index');
$routes->post('/accesos',       'Application::accesos');
$routes->post('/getroles',      'Application::getroles');
$routes->post('/asignarol',     'Application::asignarol');
$routes->post('/setpass',       'Application::setpass');
$routes->add('/cerrarsesion',   'Application::salir');
$routes->add('/testing',        'Application::testing');

$routes->get('/cursos',               'Cursos::index');
$routes->get('/cursos/crear',         'Cursos::crear');
$routes->post('/cursos/guardar',      'Cursos::guardar');
$routes->get('/cursos/editar/(:num)', 'Cursos::editar/$1');
$routes->post('/cursos/actualizar',   'Cursos::actualizar');
$routes->post('/cursos/eliminar',     'Cursos::eliminar');
$routes->get('/cursos/ver/(:num)',    'Cursos::ver/$1');

$routes->post('/secciones/guardar',   'Secciones::guardar');
$routes->post('/secciones/eliminar',  'Secciones::eliminar');

$routes->get('/lecciones',            'Lecciones::index');
$routes->post('/lecciones/guardar',   'Lecciones::guardar');
$routes->post('/lecciones/eliminar',  'Lecciones::eliminar');

$routes->get('/mi-panel/cursos',      'Panel::cursos');
$routes->get('/mi-panel/ver/(:num)',  'Panel::ver/$1');
$routes->post('/mi-panel/matricular', 'Panel::matricular');
$routes->post('/mi-panel/marcar',     'Panel::marcar');
$routes->get('/mi-panel/progreso',    'Panel::progreso');

$routes->get('/usuarios/alumnos',         'Usuarios::alumnos');
$routes->get('/usuarios/profesores',      'Usuarios::profesores');
$routes->post('/usuarios/eliminar',       'Usuarios::eliminar');
$routes->post('/usuarios/nuevo',          'Usuarios::nuevo');
$routes->post('/usuarios/cambiarestado',  'Usuarios::cambiarEstado');

$routes->get('/reportes/progreso',    'Reportes::progreso');

$routes->get('/categorias',           'Categorias::index');
$routes->post('/categorias/guardar',  'Categorias::guardar');
$routes->post('/categorias/eliminar', 'Categorias::eliminar');

if (is_file(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}

// Routes added in v2
$routes->get('/mi-panel/catalogo',    'Panel::catalogo');
$routes->get('/usuarios/misalumnos',  'Usuarios::misAlumnos');
$routes->post('/cursos/restaurar',    'Cursos::restaurar');
$routes->post('/mi-panel/resena',     'Panel::guardarResena');
$routes->get('/mi-panel/resenas/(:num)', 'Panel::resenas/$1');

// Mi Perfil (todos los perfiles)
$routes->get('/mi-perfil',           'Perfil::index');
$routes->post('/mi-perfil/guardar',  'Perfil::guardar');
$routes->post('/mi-perfil/password', 'Perfil::cambiarPassword');

// ── PERFIL ASESOR ────────────────────────────────────────────────
$routes->get('/usuarios/asesores',        'Usuarios::asesores');          // ADMIN: lista asesores
$routes->get('/asesor/cursos',            'Asesor::cursos');              // ASESOR: ver cursos
$routes->get('/asesor/alumnos/(:num)',    'Asesor::alumnos/$1');          // ASESOR: alumnos de un curso
$routes->post('/asesor/formar-grupo',     'Asesor::formarGrupo');         // ASESOR: crear grupo (POST)
$routes->get('/asesor/grupos',            'Asesor::grupos');              // ASESOR: mis grupos
$routes->get('/asesor/chat/(:num)',       'Asesor::chat/$1');             // ASESOR/ADMIN: chat grupo
$routes->post('/asesor/enviar-mensaje',   'Asesor::enviarMensaje');       // ASESOR/ADMIN: enviar msg (POST)
$routes->get('/asesor/admin-grupos',      'Asesor::adminGrupos');         // ADMIN: todos los grupos
