<?php

namespace Controller\Page\LoggedIn;

use Controller\Page\LoggedIn;
use Model\Category;
use Model\Edition;
use Util\Singleton\ErrorHandler;
use Util\Singleton\Session;
use Util\ViewRenderer;

/**
 * Categorieën pagina.
 */
class Overview extends LoggedIn
{
    public function __construct()
    {
    }

    /**
     * @return string
     */
    public function get_content(): string
    {
        return ViewRenderer::render_view('page.content.overview', [
            'categories' => Category::getAll(),
        ]);
    }

    /**
     * @return int[]
     */
    public function allowed_roles(): array
    {
        return [1, 2, 3];
    }
}
