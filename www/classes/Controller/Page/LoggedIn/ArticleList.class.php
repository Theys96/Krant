<?php

namespace Controller\Page\LoggedIn;

use Controller\Page\LoggedIn;
use Model\Article;
use Util\Singleton\ErrorHandler;
use Util\Singleton\Session;
use Util\ViewRenderer;

/**
 * Lijst pagina.
 */
abstract class ArticleList extends LoggedIn
{
    /** @var Article[] $articles */
    protected array $articles = [];

    /** @var string $title */
    protected string $title;

    /**
     * @param string $title
     */
    public function __construct(string $title)
    {
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
                $remove_article_id = (int)$_GET['place_article'];
                $article = Article::getById($remove_article_id);
                if ($article !== null) {
                    $article->moveToPlaced();
                } else {
                    ErrorHandler::instance()->addWarning('Kon stukje niet plaatsen: Niet gevonden.');
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
            'role' => Session::instance()->getRole()
        ]);
    }
}
