<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once(__DIR__ . "/../inc/Bootstrap.php");
require_once(__DIR__ . "/../inc/Router.php");
require_once(__DIR__ . "/../inc/Database.php");

// Extract the controller, action, and parameter from the URL
$request = explode('/', $_SERVER['REQUEST_URI']);
$controller = ucfirst($request[4] ?? 'Product') . 'Controller';
$action = ($request[5] ?? 'index'). 'Action';
$parameter = $request[6] ?? null;

// Instantiate the requested controller and call the action method
$controllerClass = 'App\\Controllers\\Api\\' . $controller;
if (class_exists($controllerClass)) {
    $controllerObject = new $controllerClass();
    if (method_exists($controllerObject, $action)) {
        $controllerObject->$action($parameter);
    }
} 

// Create a new instance of the Router
$router = new Inc\Router();

// Define the routes
$router->addRoute('GET', '/server/public/index.php/product', 'App\Controllers\Api\ProductController', 'listAction');
$router->addRoute('GET', '/server/public/index.php/product/{id}', 'App\Controllers\Api\ProductController', 'listAction');
$router->addRoute('DELETE', '/server/public/index.php/product/{id}', 'App\Controllers\Api\ProductController', 'deleteAction');
$router->addRoute('POST', '/serverpublic/index.php/product', 'App\Controllers\Api\ProductController', 'addAction');
$router->addRoute('POST', '/server/public/index.php/attribute', 'App\Controllers\Api\AttributeController', 'listAction');

// Route the request
$router->route();
?>