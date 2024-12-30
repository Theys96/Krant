<?php

require 'bootstrap.php';

use Util\Router;

header("Content-Type: application/json;charset=utf-8");
$controller = (new Router())->get_api_controller_instance();
echo $controller->render();
