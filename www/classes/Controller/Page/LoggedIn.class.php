<?php
namespace Controller\Page;

use Util\Singleton\ErrorHandler;
use Util\Singleton\Session;
use Util\ViewRenderer;

/**
 * Basis voor elke pagina na login.
 */
abstract class LoggedIn extends BasePage
{
    /**
     * @return string
     */
    abstract public function get_content(): string;

    /**
     * @return int[]
     */
    abstract public function allowed_roles(): array;

    /**
     * @return string
     */
    public function get_body(): string
    {
        return ViewRenderer::render_view('page.home', [
            'username' => Session::instance()->username,
            'role' => Session::instance()->role,
            'content' => $this->get_content(),
            'errors' => ErrorHandler::instance()->printAllToString()
        ]); 
    }
}
