<?php

namespace App\Controller\Page;

use App\Controller\LoggedIn;
use App\Controller\Response;
use App\Util\ViewRenderer;
use App\Util\Singleton\Session;
use App\Model\User;

/**
 * Minigame "Thijs zijn nachtmerrie".
 */
class Minigame implements Response, LoggedIn
{
    public function __construct()
    {
        if (isset($_POST['highscore'])) {
            $user = Session::instance()->getUser()->updateHighscore($_POST['highscore']);
            Session::instance()->setUser($user);
           
        }
    }
    
    public function render(): string
    {
        return ViewRenderer::render_view('minigame', [
            'highscore' => Session::instance()->getUser()->highscore,
        ]);
    }

    public function allowed_roles(): array
    {
        return [1, 2, 3];
    }
}
