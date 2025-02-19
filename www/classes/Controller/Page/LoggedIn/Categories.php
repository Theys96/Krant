<?php

namespace Controller\Page\LoggedIn;

use Controller\Page\LoggedInPage;
use Model\Category;
use Util\Singleton\ErrorHandler;
use Util\Singleton\Session;
use Util\ViewRenderer;

/**
 * Categorieën pagina.
 */
class Categories extends LoggedInPage
{
    public function __construct()
    {
        if (Session::instance()->getRole() === 3) {
            if (isset($_GET['edit_category'])) {
                $edit_category_id = (int)$_GET['edit_category'];
                $edit_name = $_POST['edit_name'];
                $edit_description = $_POST['edit_description'];
                $edit_category = Category::getById($edit_category_id);
                if ($edit_category !== null) {
                    $edit_category->update($edit_name, $edit_description);
                } else {
                    ErrorHandler::instance()->addError('Kon categorie niet aanpassen: Niet gevonden.');
                }
            }
            if (isset($_POST['new_name']) && isset($_POST['new_description'])) {
                $new_name = $_POST['new_name'];
                $new_description = $_POST['new_description'];
                $new_category = Category::createNew($new_name, $new_description);
                if ($new_category !== null) {
                    ErrorHandler::instance()->addMessage('Categorie aangemaakt.');
                }
            }
            if (isset($_GET['remove_category'])) {
                $remove_category_id = (int)$_GET['remove_category'];
                $remove_category = Category::getById($remove_category_id);
                if ($remove_category !== null) {
                    if ($remove_category->remove()) {
                        ErrorHandler::instance()->addMessage('Categorie verwijderd.');
                    } else {
                        ErrorHandler::instance()->addError('Kon categorie niet verwijderen.');
                    }
                } else {
                    ErrorHandler::instance()->addError('Kon categorie niet verwijderen: Niet gevonden.');
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
