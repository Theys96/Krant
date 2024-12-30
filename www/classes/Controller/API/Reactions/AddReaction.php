<?php

namespace Controller\API\Reactions;

use Controller\API\APIResponse;
use Model\ArticleReaction;
use Util\Singleton\Session;

class AddReaction extends APIResponse
{
    protected function get_response_object(): object|array
    {
        if (Session::instance()->isLoggedIn()) {
            if (isset($_REQUEST['article_id']) && $_REQUEST['article_id'] !== ''
                && isset($_REQUEST['reaction']) && $_REQUEST['reaction'] !== '') {
                ArticleReaction::createNew($_REQUEST['article_id'], $_REQUEST['reaction']);
                return ArticleReaction::getByArticleIdGrouped($_REQUEST['article_id']);
            }
        }
        return [];
    }
}
