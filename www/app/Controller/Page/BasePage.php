<?php

namespace App\Controller\Page;

use App\Controller\Response;
use App\Util\ViewRenderer;

/**
 * Basis voor elke pagina.
 */
abstract class BasePage implements Response
{
    /**
     * Inhoud (<body>).
     */
    abstract public function get_body(): string;

    public function render(): string
    {
        return ViewRenderer::render_view('base', [
            'body' => $this->get_body(),
            'show_easter_egg' => $this->showEasterEgg(),
        ]);
    }

    protected function showEasterEgg(): bool
    {
        return false;
    }
}
