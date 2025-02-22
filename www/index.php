<?php

require 'bootstrap.php';

use App\Util\Router;

$controller = (new Router())->get_page_controller_instance();
echo $controller->render();
