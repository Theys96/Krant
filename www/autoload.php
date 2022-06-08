
<?php
function get_class_path($class_name)
{
  $parts = explode('\\', $class_name);
  return 'classes/' . implode('/', $parts) . '.class.php';
}

spl_autoload_register(function ($class) {
  require_once get_class_path($class);
});
?>
