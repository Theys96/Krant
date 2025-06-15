<?php

namespace App\Controller\Page\LoggedIn;

use App\Controller\Page\LoggedInPage;
use App\Model\ArticleChange;
use App\Model\Category;
use App\Util\Singleton\Configuration;
use App\Util\Singleton\Session;
use App\Util\ViewRenderer;

/**
 * Nieuw stukje.
 */
class Create extends LoggedInPage
{
    public function __construct(int $article_change_type = ArticleChange::CHANGE_TYPE_NEW_ARTICLE)
    {
        if (isset($_POST['draftid'])) {
            $article_change = ArticleChange::getById((int) $_POST['draftid']);
            if (null !== $article_change && null !== $article_change->article) {
                $article_change = $article_change->updateFields(
                    $article_change->article->status,
                    $_POST['title'],
                    $_POST['text'],
                    $_POST['context'],
                    $_POST['category'],
                    isset($_POST['done']),
                    isset($_POST['picture']),
                    isset($_POST['wjd'])
                );
                $article_change = $article_change->openDraft($article_change_type);
                $article_change->article->applyChange($article_change);
            }
            header('location: ?action=list');
        }
    }

    public function get_content(): string
    {
        return ViewRenderer::render_view('page.content.create', [
            'categories' => Category::getAll(),
            'username' => Session::instance()->getUser()->username,
            'article' => null,
            'check_mode' => false,
            'title' => 'Nieuw stukje',
            'mail' => Configuration::instance()->mail_address,
        ]);
    }

    /**
     * @return int[]
     */
    public function allowed_roles(): array
    {
        return [1, 3];
    }
}
