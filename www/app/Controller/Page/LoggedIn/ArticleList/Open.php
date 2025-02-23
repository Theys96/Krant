<?php

namespace App\Controller\Page\LoggedIn\ArticleList;

use App\Controller\Page\LoggedIn\ArticleList;
use App\Model\Article;

/**
 * Geschreven stukjes pagina.
 */
class Open extends ArticleList
{
    public function __construct()
    {
        parent::__construct('Stukjes', 'list');
        $this->setArticles(Article::getAllOpen());
    }

    public function allowed_roles(): array
    {
        return [1, 2, 3];
    }
}
