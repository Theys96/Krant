<?php
namespace Util;

use Controller\Page\LoggedIn\Home;
use Controller\Page\Login;
use Controller\Page\Logout;
use Controller\Response;
use Util\Singleton\Session;
use Util\Singleton\ErrorHandler;

class Router
{
    private $paths = [];

    public function get_controller_instance(): ?Response
    {
        if (Session::instance()->logged_in) {
            if (isset($_GET['action']) && $_GET['action'] === 'logout') {
                $response = new Logout();
            } else {
                $response = new Home();
            }
        } else {
            $response = new Login();
        }

        if (is_a($response, Response::class)) {
            return $response;
        } else {
            ErrorHandler::instance()->throwFatal('Pagina kon niet worden geladen.');
        }
        return null;
    }
}
?>