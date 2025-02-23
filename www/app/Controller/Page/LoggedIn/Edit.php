<?php

namespace App\Controller\Page\LoggedIn;

use App\Model\Article;
use App\Model\ArticleChange;
use App\Model\Category;
use App\Util\Singleton\Configuration;
use App\Util\Singleton\ErrorHandler;
use App\Util\Singleton\Session;
use App\Util\ViewRenderer;

/**
 * Stukje wijzigen.
 */
class Edit extends Create
{
    protected Article $article;

    protected bool $check_mode = false;

    protected string $title = 'Stukje wijzigen';

    public function __construct(
        int $article_change_type = ArticleChange::CHANGE_TYPE_EDIT,
        bool $check_mode = false,
        string $title = 'Stukje wijzigen',
    ) {
        parent::__construct($article_change_type);
        $this->check_mode = $check_mode;
        $this->title = $title;

        if (isset($_GET['stukje'])) {
            $article = Article::getById((int) $_GET['stukje']);
            if (null !== $article) {
                $this->article = $article;

                return;
            }
        }
        ErrorHandler::instance()->addError('Stukje niet gevonden.');
    }

    public function get_content(): string
    {
        return ViewRenderer::render_view('page.content.create', [
            'categories' => Category::getAll(),
            'username' => Session::instance()->getUser()->username,
            'article' => $this->article,
            'check_mode' => $this->check_mode,
            'title' => $this->title,
            'mail' => Configuration::instance()->mail_address,
        ]);
    }
}
