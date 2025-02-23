<?php

namespace App\Util;

/**
 * Renderer for views in `/views`.
 */
class ViewRenderer
{
    public static function render_view(string $view, array $data): string
    {
        extract($data);
        ob_start();
        require 'views/'.implode('/', explode('.', $view)).'.php';

        return ob_get_clean();
    }
}
