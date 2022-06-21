<?php
namespace Controller\API\Draft;

use Controller\API\APIResponse;
use Model\Article;
use Model\ArticleChange;
use Util\Singleton\Session;

class DraftNewArticle extends APIResponse
{
    protected function get_response_object(): object|array
    {
        if (Session::instance()->isLoggedIn()) {
            $new_article = Article::createNew();
            $new_article_change = ArticleChange::createNew(
                $new_article->id,
                ArticleChange::CHANGE_TYPE_DRAFT,
                null,
                $_REQUEST['title'] ?? null,
                $_REQUEST['contents'] ?? null,
                $_REQUEST['category_id'] ?? null,
                $_REQUEST['ready'] ?? null,
                Session::instance()->getUser()->id
            );
            return [
                'draft_id' => $new_article_change->id
            ];
        }
        return [];
    }
}