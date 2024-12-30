<?php

namespace Controller\Page;

use Controller\LoggedIn;
use Controller\Response;
use Util\ViewRenderer;

/**
 * Minigame "Thijs zijn nachtmerrie"
 */
class Minigame implements Response, LoggedIn
{
    /**
     * @return string
     */
    public function render(): string
    {
        return ViewRenderer::render_view('minigame', []);
    }

    public function allowed_roles(): array
    {
        return [1, 2, 3];
    }
}
