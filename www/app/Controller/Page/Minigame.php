<?php

namespace App\Controller\Page;

use App\Controller\LoggedIn;
use App\Controller\Response;
use App\Util\ViewRenderer;
use App\Util\Singleton\Session;

/**
 * Minigame "Thijs zijn nachtmerrie".
 */
class Minigame implements Response, LoggedIn
{
    public function render(): string
    {
        return ViewRenderer::render_view('minigame', [
            'user' => Session::instance()->getUser(),
        ]);
    }

    public function allowed_roles(): array
    {
        return [1, 2, 3];
    }
}
