<?php
namespace Controller\Page\LoggedIn;

use Controller\Page\LoggedIn;
use Util\Singleton\Session;

/**
 * Home pagina.
 */
class Home extends LoggedIn
{
    /**
     * @return string
     */
    public function get_content(): string
    {
        return
            '<pre>' . var_export(Session::instance()->getUser(), true) . '</pre>';
    }

    /**
     * @return int[]
     */
    public function allowed_roles(): array
    {
        return [1,2,3];
    }
}
