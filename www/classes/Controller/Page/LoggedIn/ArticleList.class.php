<?php
namespace Controller\Page\LoggedIn;

use Controller\Page\LoggedIn;
use Model\Article;
use Model\Category;
use Util\Singleton\ErrorHandler;
use Util\Singleton\Session;
use Util\ViewRenderer;

/**
 * Lijst pagina.
 */
class ArticleList extends LoggedIn
{
    public function __construct()
    {
        if (Session::instance()->getRole() === 3) {
            if (isset($_GET['remove_article'])) {
                $remove_article_id = (int) $_GET['remove_article'];
                $article = Article::getById($remove_article_id);
                if ($article !== null) {
                    $article->moveToBin();
                } else {
                    ErrorHandler::instance()->addWarning('Kon stukje niet verwijderen: Niet gevonden.');
                }
            }
            if (isset($_GET['place_article'])) {
                $remove_article_id = (int) $_GET['place_article'];
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
     * @return string
     */
    public function get_content(): string
    {
        return ViewRenderer::render_view('page.content.list', [
            'articles' => Article::getAllOpen(),
            'title' => 'Stukjes',
            'role' => Session::instance()->getRole()
        ]);
    }

    /**
     * @return int[]
     */
    public function allowed_roles(): array
    {
       return [1,2,3];
    }
}
