<?php

namespace App\Controller\Page\LoggedIn\ArticleList;

use App\Controller\Page\LoggedIn\ArticleList;
use App\Model\Article;

/**
 * Drafts pagina.
 */
class Drafts extends ArticleList
{
    public function __construct()
    {
        parent::__construct('Drafts', 'drafts');
        $this->setArticles(Article::getAllDrafts());
    }

    public function allowed_roles(): array
    {
        return [1, 2, 3];
    }
}
