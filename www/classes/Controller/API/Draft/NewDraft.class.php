<?php

namespace Controller\API\Draft;

use Controller\API\APIResponse;
use Model\Article;
use Model\ArticleChange;
use Util\Singleton\Session;

class NewDraft extends APIResponse
{
    protected function get_response_object(): object|array
    {
        if (Session::instance()->isLoggedIn()) {

            if (isset($_REQUEST['article_id']) && $_REQUEST['article_id'] !== '') {
                $article = Article::getById($_REQUEST['article_id']);
                if ($article === null) {
                    $article = Article::createNew();
                }
            } else {
                $article = Article::createNew();
            }

            $new_article_change = ArticleChange::createNew(
                $article->id,
                ArticleChange::CHANGE_TYPE_DRAFT,
                null,
                $_REQUEST['title'] ?? null,
                $_REQUEST['contents'] ?? null,
                $_REQUEST['category_id'] ?? null,
                $_REQUEST['ready'] ?? null,
                Session::instance()->getUser()->id
            );

            if ($article->status === Article::STATUS_DRAFT) {
                $article->applyChange($new_article_change);
            }

            return [
                'draft_id' => $new_article_change->id
            ];

        }
        return [];
    }
}