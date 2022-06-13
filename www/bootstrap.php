<?php
session_start();
ini_set('display_errors', '1');
ini_set('error_reporting', 'E_ALL');
error_reporting(E_ALL);

function get_class_path($class_name): string
{
  $parts = explode('\\', $class_name);
  return 'classes/' . implode('/', $parts) . '.class.php';
}

spl_autoload_register(function ($class) {
  require_once get_class_path($class);
});
