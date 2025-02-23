<?php

namespace App\Controller\Page\LoggedIn;

use App\Controller\Page\LoggedInPage;
use App\Util\Singleton\Configuration;
use App\Util\ViewRenderer;

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
        return ViewRenderer::render_view('page.content.schrijfregels', [
            'schrijfregels' => Configuration::instance()->schrijfregels]);
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
