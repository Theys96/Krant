<?php
namespace Controller\Page;

use Controller\Page\RegularPage;
use Util\Singleton\Session;
use Util\ViewRenderer;

abstract class LoggedIn extends BasePage
{
    abstract public function get_content(): string;

    public function get_body(): string
    {
        return ViewRenderer::render_view('page.home', [
            'username' => Session::instance()->username,
            'role' => Session::instance()->role,
            'content' => $this->get_content()
        ]); 
    }
}
?>
