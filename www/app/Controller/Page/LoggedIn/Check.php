<?php

namespace App\Controller\Page\LoggedIn;

use App\Model\ArticleChange;

class Check extends Edit
{
    public function __construct(int $article_change_type = ArticleChange::CHANGE_TYPE_CHECK)
    {
        parent::__construct(
            $article_change_type,
            true,
            'Stukje nakijken'
        );
    }

    /**
     * @return int[]
     */
    public function allowed_roles(): array
    {
        return [2];
    }
}
