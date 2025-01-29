<?php

namespace Controller\Page\LoggedIn;

use Controller\Page\LoggedInPage;
use Model\Article;
use Util\Singleton\ErrorHandler;
use Util\Singleton\Session;
use Util\ViewRenderer;
use Model\SingleVariables;

/**
 * Lijst pagina.
 */
abstract class ArticleList extends LoggedInPage
{
    /** @var Article[] $articles */
    protected array $articles = [];

    /** @var string $title */
    protected string $title;

    /** @var string $list_type */
    protected string $list_type;

    /**
     * @param string $title
     * @param string $action
     */
    public function __construct(string $title, string $action)
    {
        $this->list_type = $action;
        $this->title = $title;
        if (Session::instance()->getRole() === 3) {
            if (isset($_GET['remove_article'])) {
                $remove_article_id = (int)$_GET['remove_article'];
                $article = Article::getById($remove_article_id);
                if ($article !== null) {
                    $article->moveToBin();
                } else {
                    ErrorHandler::instance()->addWarning('Kon stukje niet verwijderen: Niet gevonden.');
                }
            }
            if (isset($_GET['place_article'])) {
                $place_article_id = (int)$_GET['place_article'];
                $article = Article::getById($place_article_id);
                if ($article !== null) {
                    $article->moveToPlaced();
                } else {
                    ErrorHandler::instance()->addWarning('Kon stukje niet plaatsen: Niet gevonden.');
                }
            }
            if (isset($_GET['open_article'])) {
                $open_article_id = (int)$_GET['open_article'];
                $article = Article::getById($open_article_id);
                if ($article !== null) {
                    $article->moveToOpen();
                } else {
                    ErrorHandler::instance()->addWarning('Kon stukje niet terugzetten: Niet gevonden.');
                }
            }
        }
    }

    /**
     * @param Article[] $articles
     * @return void
     */
    protected function setArticles(array $articles): void
    {
        $this->articles = $articles;
    }

    /**
     * @return string
     */
    public function get_content(): string
    {
        return ViewRenderer::render_view('page.content.list', [
            'articles' => $this->articles,
            'title' => $this->title,
            'list_type' => $this->list_type,
            'checks' => SingleVariables::instance()->min_checks,
            'role' => Session::instance()->getRole()
        ]);
    }
}
