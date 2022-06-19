<?php
require 'bootstrap.php';

use Util\Router;

$controller = (new Router())->get_controller_instance();
echo $controller->render();
