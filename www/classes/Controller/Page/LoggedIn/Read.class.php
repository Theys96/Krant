<?php
namespace Controller\Page\LoggedIn;

use Controller\Page\LoggedIn;
use Jfcherng\Diff\Factory\RendererFactory;
use Model\Article;
use Model\ArticleChange;
use Model\Category;
use Util\Singleton\ErrorHandler;
use Util\Singleton\Session;
use Util\ViewRenderer;
use Jfcherng\Diff\Differ;

/**
 * Stukje lezen.
 */
class Read extends LoggedIn
{
    protected Article $article;

    /**
     * @param int $article_change_type
     */
    public function __construct()
    {
        if (isset($_GET['stukje'])) {
            $article = Article::getById((int) $_GET['stukje']);
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
            'article' => $this->article
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
