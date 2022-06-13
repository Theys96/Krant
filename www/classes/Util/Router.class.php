<?php
namespace Util;

use Controller\Page\LoggedIn;
use Controller\Page\LoggedIn\Home;
use Controller\Page\Login;
use Controller\Page\Logout;
use Controller\Response;
use Exception;
use Util\Singleton\ErrorHandler;
use Util\Singleton\Session;

/**
 * Controller router.
 */
class Router
{
    /** @var string[] */
    private array $actions = [
        'categories' => LoggedIn\Categories::class
    ];

    /**
     * @return Response|null
     * @throws Exception
     */
    public function get_controller_instance(): ?Response
    {
        if (Session::instance()->logged_in) {
            if (isset($_GET['action'])) {
                if ($_GET['action'] === 'logout') {
                    $response = new Logout();
                }
                elseif (array_key_exists($_GET['action'], $this->actions)) {
                    $response = new $this->actions[$_GET['action']]();
                    if ($response instanceof LoggedIn && !in_array(
                            Session::instance()->role,
                            $response->allowed_roles()
                        )) {
                        ErrorHandler::instance()->addWarning('Deze pagina is niet toegankelijk voor deze rol.');
                        $response = new Home();
                    }
                }
                else {
                    ErrorHandler::instance()->addWarning('Pagina niet gevonden.');
                    $response = new Home();
                }
            } else {
                $response = new Home();
            }
        } else {
            $response = new Login();
        }

        if (!is_a($response, Response::class)) {
            throw new Exception('Pagina kon niet worden geladen.');
        }
        return $response;
    }
}
