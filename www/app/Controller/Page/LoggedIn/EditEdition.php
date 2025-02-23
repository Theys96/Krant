<?php

namespace App\Controller\Page\LoggedIn;

use App\Controller\Page\LoggedInPage;
use App\Model\Edition;
use App\Util\Singleton\ErrorHandler;
use App\Util\ViewRenderer;

/**
 * Editie aanpassen.
 */
class EditEdition extends LoggedInPage
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
