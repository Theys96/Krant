<?php

namespace App\Controller\API\Draft;

use App\Controller\API\APIResponse;
use App\Model\Article;
use App\Model\ArticleChange;
use App\Model\User;
use App\Util\Singleton\Session;

class UpdateDraft extends APIResponse
{
    protected function get_response_object(): object|array
    {
        if (Session::instance()->isLoggedIn() && isset($_REQUEST['draft_id'])) {
            $article_change = ArticleChange::getById((int) $_REQUEST['draft_id']);
            if (null === $article_change) {
                return [];
            }

            $new_article_change = $article_change->updateFields(
                $article_change->article->status,
                $_REQUEST['title'] ?? $article_change->article->title,
                $_REQUEST['contents'] ?? $article_change->article->contents,
                $_REQUEST['context'] ?? $article_change->article->context,
                (isset($_REQUEST['category_id']) && null != $_REQUEST['category_id']) ? (is_numeric($_REQUEST['category_id']) ? (int) $_REQUEST['category_id'] : $article_change->article->category->id) : null,
                is_numeric($_REQUEST['ready']) ? (bool) $_REQUEST['ready'] : $article_change->article->ready,
            );

            if (Article::STATUS_DRAFT === $new_article_change->article->status) {
                $new_article_change->article->applyChange($new_article_change);
            }

            $warning = null;
            $live_drafters = array_filter(
                User::getLiveDrafters($new_article_change->article->id),
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
