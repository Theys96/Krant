<?php

namespace Controller\Page;

use Controller\LoggedIn;
use Util\Singleton\ErrorHandler;
use Util\Singleton\Session;
use Util\ViewRenderer;

/**
 * Basis voor elke pagina na login.
 */
abstract class LoggedInPage extends BasePage implements LoggedIn
{
    /**
     * @return string
     */
    abstract public function get_content(): string;

    /**
     * @return string
     */
    public function get_body(): string
    {
        return ViewRenderer::render_view('page.home', [
            'username' => Session::instance()->getUser()->username,
            'role' => Session::instance()->getRole(),
            'content' => $this->get_content(),
            'errors' => ErrorHandler::instance()->printAllToString()
        ]);
    }
}
