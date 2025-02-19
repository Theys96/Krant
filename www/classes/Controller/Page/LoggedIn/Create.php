<?php

namespace Controller\Page\LoggedIn;

use Controller\Page\LoggedInPage;
use Model\ArticleChange;
use Model\Category;
use Util\Singleton\Session;
use Util\Singleton\Configuration;
use Util\ViewRenderer;

/**
 * Nieuw stukje.
 */
class Create extends LoggedInPage
{
    /**
     * @param int $article_change_type
     */
    public function __construct(int $article_change_type = ArticleChange::CHANGE_TYPE_NEW_ARTICLE)
    {
        if (isset($_POST['draftid'])) {
            $article_change = ArticleChange::getById((int) $_POST['draftid']);
            if ($article_change !== null && $article_change->article !== null) {
                $article_change = $article_change->updateFields(
                    $article_change->article->status,
                    $_POST['title'],
                    $_POST['text'],
                    $_POST['context'],
                    $_POST['category'],
                    isset($_POST['done'])
                );
                $article_change = $article_change->openDraft($article_change_type);
                $article_change->article->applyChange($article_change);
            }
            header('location: ?action=list');
        }
    }

    /**
     * @return string
     */
    public function get_content(): string
    {
        return ViewRenderer::render_view('page.content.create', [
            'categories' => Category::getAll(),
            'username' => Session::instance()->getUser()->username,
            'article' => null,
            'check_mode' => false,
            'title' => 'Nieuw stukje',
            'mail' => Configuration::instance()->mail_address
        ]);
    }

    /**
     * @return int[]
     */
    public function allowed_roles(): array
    {
        return [1,3];
    }
}
