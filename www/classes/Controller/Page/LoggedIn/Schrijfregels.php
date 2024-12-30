<?php

namespace Controller\Page\LoggedIn;

use Controller\Page\LoggedInPage;
use Util\Singleton\Session;
use Util\ViewRenderer;

/**
 * Schrijfregels pagina.
 */
class Schrijfregels extends LoggedInPage
{
    /**
     * @return string
     */
    public function get_content(): string
    {
        return ViewRenderer::render_view('page.content.schrijfregels', []);
    }

    /**
     * @return int[]
     */
    public function allowed_roles(): array
    {
        return [1,2,3];
    }

    /**
     * @return bool
     */
    protected function showEasterEgg(): bool
    {
        return true;
    }
}
