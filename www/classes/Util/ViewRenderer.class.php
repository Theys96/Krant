<?php
namespace Util;

class ViewRenderer
{
    static function render_view(string $view, array $data): string
    {
        $parts = explode('.', $view);
        extract($data);
        ob_start();
        require('views/' . implode('/', $parts) . '.php');
        return ob_get_clean();
    }
}
?>
