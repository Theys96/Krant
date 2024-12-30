<?php

namespace Util;

/**
 * Renderer for views in `/views`.
 */
class ViewRenderer
{
    /**
     * @param string $view
     * @param array $data
     * @return string
     */
    public static function render_view(string $view, array $data): string
    {
        extract($data);
        ob_start();
        require('views/' . implode('/', explode('.', $view)) . '.php');
        return ob_get_clean();
    }
}
