<?php

namespace App\Controller\Page\LoggedIn;

use App\Controller\Page\LoggedInPage;
use App\Model\Category;
use App\Util\Singleton\ErrorHandler;
use App\Util\ViewRenderer;

/**
 * Categorie aanpassen.
 */
class EditCategory extends LoggedInPage
{
    protected Category $category;

    public function __construct()
    {
        if (isset($_GET['category'])) {
            $category = Category::getById((int) $_GET['category']);
            if (null !== $category) {
                $this->category = $category;

                return;
            }
        }
        ErrorHandler::instance()->addError('Categorie niet gevonden.');
    }

    public function get_content(): string
    {
        return ViewRenderer::render_view('page.content.edit_category', [
            'category' => $this->category,
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
