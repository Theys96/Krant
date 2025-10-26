<?php

namespace App\Controller\Page\LoggedIn;

use App\Controller\Page\LoggedInPage;
use App\Model\Category;
use App\Util\Singleton\Configuration;
use App\Util\ViewRenderer;

/**
 * Categorieën pagina.
 */
class Overview extends LoggedInPage
{
    public function __construct()
    {
    }

    public function get_content(): string
    {
        $configuration = Configuration::instance();

        return ViewRenderer::render_view('page.content.overview', [
            'categories' => Category::getAll(),
            'max_articles' => $configuration->max_articles,
            'max_pictures' => $configuration->max_pictures,
            'max_wjd' => $configuration->max_wjd,
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
