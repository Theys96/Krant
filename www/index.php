<?php
require 'bootstrap.php';

use Util\Router;
use Util\Singleton\Session;

\Util\Singleton\Database::instance();

Session::instance()->check_login();
$router = new Router();
$controller = $router->get_controller_instance();
echo $controller->render();
