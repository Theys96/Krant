<?php

namespace App\Controller\Page;

use App\Controller\LoggedIn;
use App\Controller\Response;
use App\Model\User;
use App\Util\Singleton\Session;
use App\Util\ViewRenderer;

/**
 * Minigame "Thijs zijn nachtmerrie".
 */
class Minigame implements Response, LoggedIn
{
    public function __construct()
    {
        if (isset($_POST['highscore'])) {
            $user = Session::instance()->getUser()->updateHighscore($_POST['highscore'], 'true' == $_POST['is_small']);
            Session::instance()->setUser($user);
        }
    }

    public function render(): string
    {
        $user = Session::instance()->getUser();

        return ViewRenderer::render_view('minigame', [
            'highscore_small' => $user->highscore_small,
            'highscore_big' => $user->highscore_big,
            'topFive' => User::getTopFive($user->id),
        ]);
    }

    public function allowed_roles(): array
    {
        return [1, 2, 3];
    }
}
