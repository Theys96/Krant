<?php

namespace Controller\Page\LoggedIn;

use Controller\Page\LoggedInPage;
use Model\Category;
use Util\Singleton\ErrorHandler;
use Util\ViewRenderer;

/**
 * Categorie aanpassen.
 */
class EditCategory extends LoggedInPage
{
    /** @var Category */
    protected Category $category;

    public function __construct()
    {
        if (isset($_GET['category'])) {
            $category = Category::getById((int)$_GET['category']);
            if ($category !== null) {
                $this->category = $category;
                return;
            }
        }
        ErrorHandler::instance()->addError('Categorie niet gevonden.');
    }


    /**
     * @return string
     */
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
