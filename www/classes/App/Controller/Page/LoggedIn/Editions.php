<?php

namespace App\Controller\Page\LoggedIn;

use App\Controller\Page\LoggedInPage;
use App\Model\Article;
use App\Model\Edition;
use App\Util\Singleton\ErrorHandler;
use App\Util\Singleton\Session;
use App\Util\ViewRenderer;

/**
 * Edities pagina.
 */
class Editions extends LoggedInPage
{
    public function __construct()
    {
        if (Session::instance()->getRole() === 3) {
            if (isset($_GET['edit_edition'])) {
                $edit_edition_id = (int)$_GET['edit_edition'];
                $edit_name = $_POST['edit_name'];
                $edit_description = $_POST['edit_description'];
                $edit_edition = Edition::getById($edit_edition_id);
                if ($edit_edition !== null) {
                    $edit_edition->update($edit_name, $edit_description);
                } else {
                    ErrorHandler::instance()->addError('Kon editie niet aanpassen: Niet gevonden.');
                }
            }
            if (isset($_POST['new_name']) && isset($_POST['new_description'])) {
                $new_name = $_POST['new_name'];
                $new_description = $_POST['new_description'];
                $new_edition = Edition::createNew($new_name, $new_description);
                if ($new_edition !== null) {
                    ErrorHandler::instance()->addMessage('Editie aangemaakt.');
                }
            }
            if (isset($_POST['active_edition'])) {
                $edit_edition = Edition::getById((int) $_POST['active_edition']);
                if ($edit_edition !== null) {
                    $edit_edition->setActive();
                } else {
                    ErrorHandler::instance()->addError('Kon editie niet activeren: Niet gevonden.');
                }
            }
            if (isset($_POST['migrate_articles'])) {
                $mapping = [];
                foreach ($_POST['from_edition_categories'] as $idx => $from_id) {
                    $mapping[(int) $from_id] = (int) $_POST['to_edition_categories'][$idx];
                }
                foreach ($_POST['migrate_articles'] as $article_id) {
                    $article = Article::getById($article_id);
                    if (array_key_exists($article->category->id, $mapping)) {
                        $article->migrateToCategory($mapping[$article->category->id]);
                    }
                }
            }
        }
    }

    /**
     * @return string
     */
    public function get_content(): string
    {
        return ViewRenderer::render_view('page.content.editions', [
            'editions' => Edition::getAll(),
            'role' => Session::instance()->getRole()
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
