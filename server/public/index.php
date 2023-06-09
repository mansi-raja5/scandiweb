<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once  __DIR__ . '/../vendor/autoload.php';

use Inc\Config;

$router = new Inc\Router();

// Define the routes
$router->addRoute('GET', Config::ROOT_URL . '/public/index.php/product', Config::PRODUCT_CONTROLLER_PATH, 'listAction');
$router->addRoute('GET', Config::ROOT_URL . '/public/index.php/product/{id}', Config::PRODUCT_CONTROLLER_PATH, 'listAction');
$router->addRoute('DELETE', Config::ROOT_URL . '/public/index.php/product', Config::PRODUCT_CONTROLLER_PATH, 'deleteAction');
$router->addRoute('POST', Config::ROOT_URL . '/public/index.php/product', Config::PRODUCT_CONTROLLER_PATH, 'addAction');
$router->addRoute('POST', Config::ROOT_URL . '/public/index.php/attribute', Config::ATTRIBUTE_CONTROLLER_PATH, 'listAction');
$router->addRoute('POST', Config::ROOT_URL . '/public/index.php/test', 'App\Controllers\Test', 'mansi');


// Route the request
$router->route();
