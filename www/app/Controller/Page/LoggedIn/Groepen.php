<?php

namespace App\Controller\Page\LoggedIn;

use App\Controller\Page\LoggedInPage;
use App\Util\ViewRenderer;

/**
 * Feedback overzicht pagina.
 */
class Groepen extends LoggedInPage
{
    public function get_content(): string
    {
        return ViewRenderer::render_view('page.content.groepen', [
        ]);
    }

    /**
     * @return int[]
     */
    public function allowed_roles(): array
    {
        return [1, 2, 3];
    }
}
