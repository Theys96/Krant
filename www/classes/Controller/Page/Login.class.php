<?php
namespace Controller\Page;

use Controller\Page\RegularPage;
use Util\Config;
use Util\Singleton\ErrorHandler;
use Util\ViewRenderer;

class Login extends BasePage
{
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
?>
