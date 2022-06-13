<?php
namespace Controller\Page;

use Util\Config;
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
            'users' => [
                1 => [
                    'Thijs'
                ],
                2 => [
                    'Thijs'
                ],
                3 => [
                    'Thijs'
                ]
            ],
            'passwords' => Config::PASSWORDS,
            'errors' => ErrorHandler::instance()->printAllToString()
        ]); 
    }
}
