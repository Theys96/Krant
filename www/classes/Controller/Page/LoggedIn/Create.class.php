<?php
namespace Controller\Page\LoggedIn;

use Controller\Page\LoggedIn;
use Model\Article;
use Model\ArticleChange;
use Model\Category;
use Util\Singleton\Session;
use Util\ViewRenderer;

/**
 * Nieuw stukje.
 */
class Create extends LoggedIn
{
    public function __construct()
    {
        if (isset($_POST['draftid'])) {
            $article_change = ArticleChange::getById((int) $_POST['draftid']);
            if ($article_change !== null && $article_change->article !== null) {
                $article_change = $article_change->updateFields(
                    $_POST['title'] ?? null,
                    $_POST['text'] ?? null,
                    $_POST['category'] ?? null,
                    $_POST['done'] ?? false,
                );
                $article_change = $article_change->openDraft(ArticleChange::CHANGE_TYPE_NEW_ARTICLE);
                $article_change->article->applyChange($article_change);
            }
        }
    }

    /**
     * @return string
     */
    public function get_content(): string
    {
        return ViewRenderer::render_view('page.content.create', [
            'categories' => Category::getAll(),
            'username' => Session::instance()->getUser()->username
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
