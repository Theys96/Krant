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
                $user_id = Session::instance()->getUser()?->id;
                ArticleReaction::createNew($_REQUEST['article_id'], $_REQUEST['reaction'], $user_id);
                return ArticleReaction::getByArticleIdGrouped($_REQUEST['article_id']);
            }
        }
        return [];
    }
}
