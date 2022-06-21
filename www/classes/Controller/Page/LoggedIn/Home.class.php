<?php
namespace Controller\Page\LoggedIn;

use Controller\Page\LoggedIn;
use Model\Article;
use Model\ArticleChange;
use Util\Singleton\Session;

/**
 * Home pagina.
 */
class Home extends LoggedIn
{
    /**
     * @return string
     */
    public function get_content(): string
    {
        $new_article = Article::createNew();
        $new_article_change = ArticleChange::createNew(
            $new_article->id,
            ArticleChange::CHANGE_TYPE_DEFAULT,
            null,
            "Hello world!",
            null,
            null,
            null,
            1
        );
        $new_article->applyChange($new_article_change);
        return
            '<pre>' . var_export(Session::instance()->getUser(), true) . '</pre>' .
            '<pre>' . var_export($new_article_change, true) . '</pre>' .
            '<pre>' . var_export($new_article_change->article, true) . '</pre>';
    }

    /**
     * @return int[]
     */
    public function allowed_roles(): array
    {
        return [1,2,3];
    }
}
