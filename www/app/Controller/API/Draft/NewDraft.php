<?php

namespace App\Controller\API\Draft;

use App\Controller\API\APIResponse;
use App\Model\Article;
use App\Model\ArticleChange;
use App\Model\User;
use App\Util\Singleton\Session;

class NewDraft extends APIResponse
{
    protected function get_response_object(): object|array
    {
        if (Session::instance()->isLoggedIn()) {
            if (isset($_REQUEST['article_id']) && '' !== $_REQUEST['article_id']) {
                $article = Article::getById($_REQUEST['article_id']);
                if (null === $article) {
                    $article = Article::createNew();
                }
            } else {
                $article = Article::createNew();
            }

            $new_article_change = ArticleChange::createNew(
                $article->id,
                ArticleChange::CHANGE_TYPE_DRAFT,
                $article->status,
                $_REQUEST['title'] ?? $article->title,
                $_REQUEST['contents'] ?? $article->contents,
                $_REQUEST['context'] ?? $article->context,
                is_numeric($_REQUEST['category_id']) ? (int) $_REQUEST['category_id'] : $article->category->id,
                is_numeric($_REQUEST['ready']) ? (bool) $_REQUEST['ready'] : $article->ready,
                Session::instance()->getUser()->id
            );

            if (Article::STATUS_DRAFT === $article->status) {
                $article->applyChange($new_article_change);
            }

            $warning = null;
            $live_drafters = array_filter(
                User::getLiveDrafters($article->id),
                static function (User $user): bool {
                    return $user->id !== Session::instance()->getUser()->id;
                }
            );
            if (count($live_drafters) > 0) {
                $names = implode(', ', array_column($live_drafters, 'username'));
                $warning = htmlspecialchars($names).(count($live_drafters) > 1 ? ' werken ' : ' werkt ').'nu ook aan dit stukje.';
            }

            return [
                'draft_id' => $new_article_change->id,
                'warning' => $warning,
            ];
        }

        return [];
    }
}
