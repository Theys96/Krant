<?php

namespace App\Controller\API\Reactions;

use App\Controller\API\APIResponse;
use App\Model\ArticleReaction;
use App\Util\Singleton\Session;

class AddReaction extends APIResponse
{
    protected function get_response_object(): object|array
    {
        if (Session::instance()->isLoggedIn()) {
            if (isset($_REQUEST['article_id']) && '' !== $_REQUEST['article_id']
                && isset($_REQUEST['reaction']) && '' !== $_REQUEST['reaction']) {
                $user_id = Session::instance()->getUser()?->id;
                $current_reaction = ArticleReaction::getByArticleIdAndUserId($_REQUEST['article_id'], $user_id);
                if (null !== $current_reaction && $current_reaction->reaction === $_REQUEST['reaction']) {
                    $current_reaction->delete();
                } else {
                    ArticleReaction::createNew($_REQUEST['article_id'], $_REQUEST['reaction'], $user_id);
                }

                return ArticleReaction::getByArticleIdGrouped($_REQUEST['article_id']);
            }
        }

        return [];
    }
}
