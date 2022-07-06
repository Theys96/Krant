<?php

namespace Controller\Page\LoggedIn;

use Controller\Page\LoggedIn;
use Model\Article;
use Model\Category;
use Util\Singleton\ErrorHandler;
use Util\Singleton\Session;
use Util\ViewRenderer;

/**
 * Categorie aanpassen.
 */
class EditCategory extends LoggedIn
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
