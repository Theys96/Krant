<?php

namespace App\Controller\Page\LoggedIn;

use App\Controller\Page\LoggedInPage;
use App\Model\Article;
use App\Model\Edition;
use App\Util\Singleton\ErrorHandler;
use App\Util\ViewRenderer;

/**
 * Editie stukjes migreren.
 */
class MigrateEdition extends LoggedInPage
{
    protected Edition $edition;

    public function __construct()
    {
        if (isset($_GET['edition'])) {
            $edition = Edition::getById((int) $_GET['edition']);
            if (null !== $edition) {
                $this->edition = $edition;

                return;
            }
        }
        ErrorHandler::instance()->addError('Editie niet gevonden.');
    }

    public function get_content(): string
    {
        return ViewRenderer::render_view('page.content.migrate_edition', [
            'from_edition' => $this->edition,
            'to_edition' => Edition::getActive(),
            'articles' => array_filter(
                Article::getAllByEdition($this->edition),
                static function (Article $article) {
                    return Article::STATUS_OPEN === $article->status;
                }
            ),
        ]);
    }

    /**
     * @return int[]
     */
    public function allowed_roles(): array
    {
        return [3];
    }
}
