<?php

namespace App\Controller\API\Reactions;

use App\Controller\API\APIResponse;
use App\Model\ArticleReaction;
use App\Util\Singleton\Session;

class FetchReactions extends APIResponse
{
    protected function get_response_object(): object|array
    {
        if (Session::instance()->isLoggedIn()) {
            if (isset($_REQUEST['article_id']) && '' !== $_REQUEST['article_id']) {
                return ArticleReaction::getByArticleIdGrouped($_REQUEST['article_id']);
            }
        }

        return [];
    }
}
