<?php
declare(strict_types=1);

use Cake\Routing\Router;
use Cake\Routing\RouteBuilder;

return static function (RouteBuilder $routes): void {
    $routes->setRouteClass(Cake\Routing\Route\DashedRoute::class);

    // Rutas del login mínimo
    $routes->connect('/', ['controller' => 'Users', 'action' => 'loginForm']);
    $routes->connect('/login', ['controller' => 'Users', 'action' => 'login', '_method' => 'POST']);
    $routes->connect('/app', ['controller' => 'Pages', 'action' => 'display', 'home']);
    
    // Rutas de administración
    $routes->connect('/admin', ['controller' => 'Users', 'action' => 'adminPanel']);
    $routes->connect('/admin/users', ['controller' => 'Users', 'action' => 'adminPanel']);
    $routes->connect('/admin/users-list', ['controller' => 'Users', 'action' => 'getUsersList']);
    $routes->connect('/admin/create-user', ['controller' => 'Users', 'action' => 'createUser', '_method' => 'POST']);
    $routes->connect('/admin/delete-user', ['controller' => 'Users', 'action' => 'deleteUser', '_method' => 'POST']);

    $routes->fallbacks();
};
