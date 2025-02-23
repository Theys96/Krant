<?php

namespace App\Controller\Page\LoggedIn;

use App\Controller\Page\LoggedInPage;
use App\Model\Article;
use App\Util\Singleton\Configuration;
use App\Util\Singleton\ErrorHandler;
use App\Util\Singleton\Session;
use App\Util\ViewRenderer;

/**
 * Lijst pagina.
 */
abstract class ArticleList extends LoggedInPage
{
    /** @var Article[] */
    protected array $articles = [];

    protected string $title;

    protected string $list_type;

    public function __construct(string $title, string $action)
    {
        $this->list_type = $action;
        $this->title = $title;
        if (3 === Session::instance()->getRole()) {
            if (isset($_GET['remove_article'])) {
                $remove_article_id = (int) $_GET['remove_article'];
                $article = Article::getById($remove_article_id);
                if (null !== $article) {
                    $article->moveToBin();
                } else {
                    ErrorHandler::instance()->addWarning('Kon stukje niet verwijderen: Niet gevonden.');
                }
            }
            if (isset($_GET['place_article'])) {
                $place_article_id = (int) $_GET['place_article'];
                $article = Article::getById($place_article_id);
                if (null !== $article) {
                    $article->moveToPlaced();
                } else {
                    ErrorHandler::instance()->addWarning('Kon stukje niet plaatsen: Niet gevonden.');
                }
            }
            if (isset($_GET['open_article'])) {
                $open_article_id = (int) $_GET['open_article'];
                $article = Article::getById($open_article_id);
                if (null !== $article) {
                    $article->moveToOpen();
                } else {
                    ErrorHandler::instance()->addWarning('Kon stukje niet terugzetten: Niet gevonden.');
                }
            }
        }
    }

    /**
     * @param Article[] $articles
     */
    protected function setArticles(array $articles): void
    {
        $this->articles = $articles;
    }

    public function get_content(): string
    {
        return ViewRenderer::render_view('page.content.list', [
            'articles' => $this->articles,
            'title' => $this->title,
            'list_type' => $this->list_type,
            'checks' => Configuration::instance()->min_checks,
            'role' => Session::instance()->getRole(),
        ]);
    }
}
