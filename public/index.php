<?php

require dirname(__DIR__) . "/vendor/autoload.php";


/**
 * Error and Exception handling
 */
error_reporting(E_ALL);
set_error_handler("Core\Error::errorHandler");
set_exception_handler("Core\Error::exceptionHandler");


/**
 * Routing
 */
$router = new Core\Router();

$router->add("", ["controller" => "Sign_in", "action" => "index"]);
$router->add("sign_in/",["controller"=>"Sign_in","action"=>"sign_in"]);

$router->add("sign_up/",["controller" => "Sign_up", "action" => "index"]);
$router->add("sign_up/registration",["controller" => "Sign_up", "action" => "registration"]);

$router->add("import/",["controller" => "Import", "action" => "Import"]);

$router->dispatch($_SERVER['QUERY_STRING']);
