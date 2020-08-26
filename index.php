<?php

require_once('./vendor/autoload.php');
require_once('./config.php');

use Classes\Router;

$router = new Router();

// Not Found
$router->any('NotFoundController@index');
