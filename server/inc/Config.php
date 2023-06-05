<?php

namespace Inc;

class Config
{
    public function __construct()
    {
        //database constant
        define("DB_HOST", "localhost");
        define("DB_USERNAME", "root");
        define("DB_PASSWORD", "");
        define("DB_DATABASE_NAME", "PMS");

        define("ROOT_PATH", __DIR__ . "/..");
        define("ROOT_URL", "http://mancs-macbook-pro.local/scandiweb/server");
    }
}
