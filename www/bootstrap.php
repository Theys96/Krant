<?php

require_once './vendor/autoload.php';
session_start();
ini_set('display_errors', '1');
ini_set('error_reporting', 'E_ALL');
error_reporting(E_ALL);

use App\Util\Singleton\ErrorHandler;

set_exception_handler([ErrorHandler::class, 'exceptionHandler']);
