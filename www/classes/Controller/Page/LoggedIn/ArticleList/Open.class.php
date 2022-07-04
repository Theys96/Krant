<?php

namespace Controller\Page\LoggedIn\ArticleList;

use Controller\Page\LoggedIn\ArticleList;
use Model\Article;

/**
 * Geschreven stukjes pagina.
 */
class Open extends ArticleList
{
    public function __construct()
    {
        parent::__construct('Stukjes');
        $this->setArticles(Article::getAllOpen());
    }

    public function allowed_roles(): array
    {
        return [1, 2, 3];
    }
}
