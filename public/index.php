<?php

require dirname(__DIR__) . '/vendor/autoload.php';

header('Access-Control-Allow-Origin: *');
/**
 * Error and Exception handling
 */
error_reporting(E_ALL);
set_error_handler('Core\Error::errorHandler');
set_exception_handler('Core\Error::exceptionHandler');

/**
 * Routing
 */
$router = new Core\Router();
$db = new App\Models\Users();
$api = new App\Models\API();

if ($db->isAuth()) {
    $router->add('/import/', ['controller' => 'Import', 'action' => 'index']);
    $router->add('/import/import/', ['controller' => 'Import', 'action' => 'parseUploadFile']);
    $router->add('/import/insertTable/', ['controller' => 'Import', 'action' => 'insertTable']);

    $router->add('/table/{page:\d+}', ['controller' => 'Table', 'action' => 'index']);
    $router->add('/search/{word:[a-zA-z0-9%]+}/{page:\d+}', ['controller' => 'Table', 'action' => 'index']);

    $router->add('/api/', ['controller' => 'API', 'action' => 'index']);
    $router->add('/api/token/{hash:[a-zA-z0-9]+}', ['controller' => 'API', 'action' => 'getToken']);

    $router->add('/export/', ['controller' => 'Export', 'action' => 'index']);
}

$router->add('/api/clients/', ['controller' => 'API', 'action' => 'chooseMethod']);

$router->add('/', ['controller' => 'SignIn', 'action' => 'index']);
$router->add('/sign_in/', ['controller' => 'SignIn', 'action' => 'signIn']);

$router->add('/sign_up/', ['controller' => 'SignUp', 'action' => 'index']);
$router->add('/sign_up/registration/', ['controller' => 'SignUp', 'action' => 'registration']);
$router->add('/sign_up/emailVerification/', ['controller' => 'SignUp', 'action' => 'emailVerification']);

try {
    $router->dispatch($_SERVER['REQUEST_URI']);
} catch (Exception $e) {
    http_response_code(404);
    echo 'Страница не найдена';
}

