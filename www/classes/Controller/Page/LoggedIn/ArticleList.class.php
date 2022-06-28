<?php
namespace Controller\Page\LoggedIn;

use Controller\Page\LoggedIn;
use Model\Article;
use Model\Category;
use Util\Singleton\Session;
use Util\ViewRenderer;

/**
 * Lijst pagina.
 */
class ArticleList extends LoggedIn
{
    /**
     * @return string
     */
    public function get_content(): string
    {
        return ViewRenderer::render_view('page.content.list', [
            'articles' => Article::getAllOpen(),
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
