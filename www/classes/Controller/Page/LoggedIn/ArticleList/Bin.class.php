<?php

namespace Controller\Page\LoggedIn\ArticleList;

use Controller\Page\LoggedIn\ArticleList;
use Model\Article;

/**
 * Verwijderde stukjes pagina.
 */
class Bin extends ArticleList
{
    public function __construct()
    {
        parent::__construct('Prullenbak', 'bin');
        $this->setArticles(Article::getAllBinned());
    }

    public function allowed_roles(): array
    {
        return [1, 2, 3];
    }
}
