<?php

namespace Controller\Page;

use Controller\Response;
use Util\ViewRenderer;

/**
 * Basis voor elke pagina.
 */
abstract class BasePage implements Response
{
    /**
     * Inhoud (<body>)
     *
     * @return string
     */
    abstract public function get_body(): string;

    /**
     * @return string
     */
    public function render(): string
    {
        return ViewRenderer::render_view('base', [
            'body' => $this->get_body(),
            'show_easter_egg' => $this->showEasterEgg(),
        ]);
    }

    /**
     * @return false
     */
    protected function showEasterEgg(): bool
    {
        return false;
    }
}
