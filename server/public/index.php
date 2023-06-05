<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once  __DIR__ . '/../vendor/autoload.php';

$config = new Inc\Config();
$router = new Inc\Router();

// Define the routes
$router->addRoute('GET', ROOT_URL . '/product', 'App\Controllers\Api\ProductController', 'listAction');
$router->addRoute('GET', ROOT_URL . '/product/{id}', 'App\Controllers\Api\ProductController', 'listAction');
$router->addRoute('DELETE', ROOT_URL . '/product/{id}', 'App\Controllers\Api\ProductController', 'deleteAction');
$router->addRoute('POST', ROOT_URL . '/product', 'App\Controllers\Api\ProductController', 'addAction');
$router->addRoute('POST', ROOT_URL . '/attribute', 'App\Controllers\Api\AttributeController', 'listAction');

// Route the request
$router->route();
