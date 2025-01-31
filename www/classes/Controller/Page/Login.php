<?php

namespace Controller\Page;

use Model\User;
use Util\Singleton\Configuration;
use Util\Singleton\ErrorHandler;
use Util\ViewRenderer;

/**
 * Login pagina.
 */
class Login extends BasePage
{
    /**
     * @return string
     */
    public function get_body(): string
    {
        return ViewRenderer::render_view('page.login', [
            'users' => User::getAllActive(),
            'passwords' => Configuration::instance()->passwords,
            'errors' => ErrorHandler::instance()->printAllToString()
        ]);
    }
}
