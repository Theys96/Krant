<?php
namespace Controller\Page\LoggedIn;

use Controller\Page\LoggedIn;
use Model\Article;
use Model\ArticleChange;
use Model\Category;
use Util\Singleton\ErrorHandler;
use Util\Singleton\Session;
use Util\ViewRenderer;

/**
 * Stukje wijzigen.
 */
class Edit extends Create
{
    /** @var Article */
    protected Article $article;

    public function __construct()
    {
        parent::__construct(ArticleChange::CHANGE_TYPE_EDIT);
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
        return ViewRenderer::render_view('page.content.create', [
            'categories' => Category::getAll(),
            'username' => Session::instance()->getUser()->username,
            'article' => $this->article,
        ]);
    }
}
