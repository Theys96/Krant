<?php

namespace Controller\Page\LoggedIn;

use Controller\Page\LoggedIn;
use Model\Article;
use Model\Category;
use Model\Edition;
use Util\Singleton\ErrorHandler;
use Util\Singleton\Session;
use Util\ViewRenderer;

/**
 * Editie aanpassen.
 */
class EditEdition extends LoggedIn
{
    /** @var Edition */
    protected Edition $edition;

    public function __construct()
    {
        if (isset($_GET['edition'])) {
            $edition = Edition::getById((int)$_GET['edition']);
            if ($edition !== null) {
                $this->edition = $edition;
                return;
            }
        }
        ErrorHandler::instance()->addError('Editie niet gevonden.');
    }


    /**
     * @return string
     */
    public function get_content(): string
    {
        return ViewRenderer::render_view('page.content.edit_edition', [
            'edition' => $this->edition,
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
