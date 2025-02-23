<?php

namespace App\Controller\Page\LoggedIn;

use App\Controller\Page\LoggedInPage;
use App\Model\Article;
use App\Model\ArticleChange;
use App\Util\Singleton\ErrorHandler;
use App\Util\Singleton\Session;
use App\Util\ViewRenderer;

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
