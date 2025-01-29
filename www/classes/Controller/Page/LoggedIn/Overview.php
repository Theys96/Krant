<?php

namespace Controller\Page\LoggedIn;

use Controller\Page\LoggedInPage;
use Model\Category;
use Util\ViewRenderer;

/**
 * Categorieën pagina.
 */
class Overview extends LoggedInPage
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
