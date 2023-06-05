<?php

namespace Inc;

class Config
{
    // Database constants
    const DB_HOST = "localhost";
    const DB_USERNAME = "root";
    const DB_PASSWORD = "";
    const DB_DATABASE_NAME = "PMS";

    // Other constants
    const ROOT_PATH = __DIR__ . "/..";
    const ROOT_URL = "http://mancs-macbook-pro.local/scandiweb/server";

    // Class constants
    const PRODUCT_CONTROLLER_PATH = 'App\Controllers\Api\ProductController';
    const ATTRIBUTE_CONTROLLER_PATH = 'App\Controllers\Api\AttributeController';
}
