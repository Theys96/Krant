<?php
session_start();
ini_set('display_errors', '1');
ini_set('error_reporting', 'E_ALL');
error_reporting(E_ALL);
require 'autoload.php';

use Util\Router;
use Util\Singleton\Session;

Session::instance()->check_login();
$router = new Router();
$controller = $router->get_controller_instance();
echo $controller->render();
?>