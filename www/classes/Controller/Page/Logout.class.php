<?php
namespace Controller\Page;

use Util\Singleton\Session;

/**
 * Uitloggen (wordt dan dus een login pagina).
 */
class Logout extends Login
{
    /**
     * Reset session to log out.
     */
    public function __construct()
    {
        Session::instance()->reset(); 
    }
}
