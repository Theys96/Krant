<?php
require 'bootstrap.php';

use Util\Router;
use Util\Singleton\Session;

Session::instance()->check_login();
$router = new Router();
$controller = $router->get_controller_instance();
echo $controller->render();
