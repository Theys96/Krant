<?php
namespace Controller\Page\LoggedIn;

use Controller\Page\LoggedIn;
use Jfcherng\Diff\Factory\RendererFactory;
use Model\Article;
use Model\ArticleChange;
use Model\Category;
use Util\Singleton\Session;
use Util\ViewRenderer;
use Jfcherng\Diff\Differ;

/**
 * Nieuw stukje.
 */
class Create extends LoggedIn
{
    /**
     * @param int $article_change_type
     */
    public function __construct(int $article_change_type = ArticleChange::CHANGE_TYPE_NEW_ARTICLE)
    {
        if (isset($_POST['draftid'])) {
            $article_change = ArticleChange::getById((int) $_POST['draftid']);
            if ($article_change !== null && $article_change->article !== null) {
                $article_change = $article_change->updateFields(
                    $article_change->article->status,
                    $_POST['title'] ?? $article_change->article->title,
                    $_POST['text'] ?? $article_change->article->contents,
                    $_POST['category'] ?? $article_change->article->category->id,
                    $_POST['done'] ?? $article_change->article->ready,
                );
                $article_change = $article_change->openDraft($article_change_type);
//                $differ = new Differ(
//                    explode(PHP_EOL, $article_change->article->contents),
//                    explode(PHP_EOL, $article_change->changed_contents)
//                );
//                $renderer = RendererFactory::make('Inline'); // or your own renderer object
//                $this->diff = $renderer->render($differ);
                $article_change->article->applyChange($article_change);
            }
            header('location: ?action=list');
        }
    }

    /**
     * @return string
     */
    public function get_content(): string
    {
        return ViewRenderer::render_view('page.content.create', [
            'categories' => Category::getAll(),
            'username' => Session::instance()->getUser()->username,
            'article' => null,
            'check_mode' => false,
            'title' => 'Nieuw stukje'
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
