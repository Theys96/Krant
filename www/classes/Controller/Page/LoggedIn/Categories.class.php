<?php
namespace Controller\Page\LoggedIn;

use Controller\Page\LoggedIn;
use Model\Category;
use Util\ViewRenderer;

/**
 * Categorieën pagina.
 */
class Categories extends LoggedIn
{
    /**
     * @return string
     */
    public function get_content(): string
    {
        return ViewRenderer::render_view('page.content.categories', [
            'categories' => Category::getAll()
        ]);
    }

    /**
     * @return int[]
     */
    public function allowed_roles(): array
    {
       return [3];
    }
}
