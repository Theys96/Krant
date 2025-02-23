<?php

namespace App\Controller\Page;

use App\Controller\LoggedIn;
use App\Util\Singleton\ErrorHandler;
use App\Util\Singleton\Session;
use App\Util\ViewRenderer;

/**
 * Basis voor elke pagina na login.
 */
abstract class LoggedInPage extends BasePage implements LoggedIn
{
    abstract public function get_content(): string;

    public function get_body(): string
    {
        return ViewRenderer::render_view('page.home', [
            'username' => Session::instance()->getUser()->username,
            'role' => Session::instance()->getRole(),
            'content' => $this->get_content(),
            'errors' => ErrorHandler::instance()->printAllToString(),
        ]);
    }
}
