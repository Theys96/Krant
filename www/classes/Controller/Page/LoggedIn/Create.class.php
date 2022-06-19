<?php
namespace Controller\Page\LoggedIn;

use Controller\Page\LoggedIn;
use Model\Category;
use Util\Singleton\Session;
use Util\ViewRenderer;

/**
 * Nieuw stukje.
 */
class Create extends LoggedIn
{
    /**
     * @return string
     */
    public function get_content(): string
    {
        return ViewRenderer::render_view('page.content.create', [
            'categories' => Category::getAll(),
            'username' => Session::instance()->getUsername()
        ]);
    }

    /**
     * @return int[]
     */
    public function allowed_roles(): array
    {
       return [1,3];
    }
}
