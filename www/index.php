<?php
require 'bootstrap.php';

use Util\Router;
use Util\Singleton\Session;

Session::instance()->check_login();
$controller = (new Router())->get_controller_instance();
echo $controller->render();
