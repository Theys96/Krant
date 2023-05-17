<?php

namespace Controller\Page\LoggedIn\ArticleList;

use Controller\Page\LoggedIn\ArticleList;
use Model\Article;

/**
 * Geplaatste stukjes pagina.
 */
class Placed extends ArticleList
{
    public function __construct()
    {
        parent::__construct('Geplaatst', 'placed');
        $this->setArticles(Article::getAllPlaced());
    }

    public function allowed_roles(): array
    {
        return [1, 2, 3];
    }
}
