<?php
session_start();
ini_set('display_errors', '1');
ini_set('error_reporting', 'E_ALL');
error_reporting(E_ALL);

/**
 * @param $class_name
 * @return string
 * @throws Exception
 */
function get_class_path($class_name): string
{
  $parts = explode('\\', $class_name);
  $filename = 'classes/' . implode('/', $parts) . '.class.php';
  $filename_alt = 'classes/' . implode('/', $parts) . '.php';
  if (!file_exists($filename) && file_exists($filename_alt)) {
      return 'classes/' . implode('/', $parts) . '.php';
      //throw new Exception(sprintf('File %s should be renamed to %s.', $filename_alt, $filename));
  }
  return 'classes/' . implode('/', $parts) . '.class.php';
}

spl_autoload_register(function ($class) {
  require_once get_class_path($class);
});

use Util\Singleton\ErrorHandler;
set_exception_handler([ErrorHandler::class, 'exceptionHandler']);
