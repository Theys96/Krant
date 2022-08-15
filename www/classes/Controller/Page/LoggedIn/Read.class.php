<?php

namespace Controller\Page\LoggedIn;

use Controller\Page\LoggedIn;
use Model\Article;
use Util\Singleton\ErrorHandler;
use Util\Singleton\Session;
use Util\ViewRenderer;

/**
 * Stukje lezen.
 */
class Read extends LoggedIn
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
            'role' => Session::instance()->getRole()
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
