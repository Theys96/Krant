<?php

namespace Controller\Page\LoggedIn;

use Controller\Page\LoggedIn;
use Model\Category;
use Util\Singleton\ErrorHandler;
use Util\Singleton\Session;
use Util\ViewRenderer;

/**
 * Categorieën pagina.
 */
class Categories extends LoggedIn
{
    public function __construct()
    {
        if (Session::instance()->getRole() === 3) {
            if (isset($_GET['edit_category'])) {
                $edit_category_id = (int)$_GET['edit_category'];
                $edit_title = $_POST['edit_title'];
                $edit_description = $_POST['edit_description'];
                $edit_category = Category::getById($edit_category_id);
                if ($edit_category !== null) {
                    $edit_category->update($edit_title, $edit_description);
                } else {
                    ErrorHandler::instance()->addError('Kon categorie niet aanpassen: Niet gevonden.');
                }
            }
        }
    }

    /**
     * @return string
     */
    public function get_content(): string
    {
        return ViewRenderer::render_view('page.content.categories', [
            'categories' => Category::getAll(),
            'role' => Session::instance()->getRole()
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
