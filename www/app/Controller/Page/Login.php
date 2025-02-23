<?php

namespace App\Controller\Page;

use App\Model\User;
use App\Util\Singleton\Configuration;
use App\Util\Singleton\ErrorHandler;
use App\Util\ViewRenderer;

/**
 * Login pagina.
 */
class Login extends BasePage
{
    public function get_body(): string
    {
        return ViewRenderer::render_view('page.login', [
            'users' => User::getAllActive(),
            'passwords' => Configuration::instance()->passwords,
            'errors' => ErrorHandler::instance()->printAllToString(),
        ]);
    }
}
