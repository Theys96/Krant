<?php

namespace Controller\Page\LoggedIn;

use Controller\Page\LoggedInPage;
use Model\Article;
use Model\ArticleChange;
use Util\Singleton\ErrorHandler;
use Util\Singleton\Session;
use Util\ViewRenderer;

/**
 * Stukje lezen.
 */
class Read extends LoggedInPage
{
    /** @var Article */
    protected Article $article;

    public function __construct()
    {
        if (isset($_GET['stukje'])) {
            $article = Article::getById((int)$_GET['stukje']);
            if ($article !== null) {
                $this->article = $article;
                return;
            }
        }
        ErrorHandler::instance()->addError('Stukje niet gevonden.');
    }

    /**
     * @return string
     */
    public function get_content(): string
    {
        return ViewRenderer::render_view('page.content.read', [
            'article' => $this->article,
            'article_changes' => ArticleChange::getByArticleId($this->article->id),
            'role' => Session::instance()->getRole(),
            'source' => $_GET['source'] ?? 'list'
        ]);
    }

    /**
     * @return int[]
     */
    public function allowed_roles(): array
    {
        return [1, 2, 3];
    }
}
